<?php
class FarmerController {

    private ProduceModel $produce;
    private OrderModel   $order;
    private NotificationModel $notif;

    public function __construct() {
        Auth::requireRole(['farmer']);
        $this->produce = new ProduceModel();
        $this->order   = new OrderModel();
        $this->notif   = new NotificationModel();
    }

    /* ── Dashboard ──────────────────────────────────────────────────── */
    public function dashboard(): void {
        $farmerId  = Session::userId();
        $listings  = $this->produce->getByFarmer($farmerId);
        $orders    = $this->order->getBySeller($farmerId);
        $user      = (new UserModel())->findById($farmerId);

        $stats = [
            'active_listings' => count(array_filter($listings, fn($l) => $l['status'] === 'available')),
            'total_orders'    => count($orders),
            'pending_orders'  => count(array_filter($orders, fn($o) => $o['status'] === 'pending')),
            'total_revenue'   => array_sum(array_column(
                array_filter($orders, fn($o) => in_array($o['status'], ['delivered', 'completed'])),
                'total_price'
            )),
        ];
        $recentOrders  = array_slice($orders, 0, 5);
        $notifications = $this->notif->getForUser($farmerId, 5);

        // Low stock alert listings
        $lowStock = $this->produce->getLowStock($farmerId);

        // Regional demand — top categories buyers want in this farmer's region
        $farmerRegion  = $user['region'] ?? '';
        $regionalDemand = $farmerRegion
            ? $this->produce->getRegionalDemand($farmerRegion, 5)
            : [];

        // ReviewModel for farmer rating
        $reviewModel  = new ReviewModel();
        $farmerRating = $reviewModel->getAvgRating($farmerId);

        $pageTitle = 'Farmer Dashboard';
        include BASE_PATH . '/app/views/farmer/dashboard.php';
    }

    /* ── Listings list ───────────────────────────────────────────────── */
    public function listings(): void {
        $farmerId = Session::userId();
        $listings = $this->produce->getByFarmer($farmerId);
        $pageTitle = 'My Listings';
        include BASE_PATH . '/app/views/farmer/listings.php';
    }

    /* ── Add listing form ────────────────────────────────────────────── */
    public function addListing(): void {
        $pageTitle = 'Add New Listing';
        $errors = $old = [];
        include BASE_PATH . '/app/views/farmer/add_listing.php';
    }

    public function doAddListing(): void {
        Auth::requireRole(['farmer']);
        if (!Session::verifyCsrf($_POST['_token'] ?? '')) {
            Session::setFlash('error', 'Invalid request token.');
            Auth::redirect('/farmer/listings/add');
        }

        $farmerId = Session::userId();
        $errors   = [];

        $name        = sanitize($_POST['name'] ?? '');
        $category    = sanitize($_POST['category'] ?? '');
        $quantity    = (float)($_POST['quantity'] ?? 0);
        $unit        = sanitize($_POST['unit'] ?? 'kg');
        $price       = (float)($_POST['price_per_unit'] ?? 0);
        $region      = sanitize($_POST['region'] ?? '');
        $town        = sanitize($_POST['town'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $harvestDate = sanitize($_POST['harvest_date'] ?? '');

        if (!$name)     $errors[] = 'Produce name is required.';
        if (!$category) $errors[] = 'Category is required.';
        if ($quantity <= 0) $errors[] = 'Quantity must be greater than zero.';
        if ($price <= 0)    $errors[] = 'Price must be greater than zero.';
        if (!$region)  $errors[] = 'Region is required.';

        if ($errors) {
            Session::setFlash('error', implode('<br>', $errors));
            Auth::redirect('/farmer/listings/add');
        }

        $this->produce->create([
            'farmer_id'     => $farmerId,
            'name'          => $name,
            'category'      => $category,
            'quantity'      => $quantity,
            'unit'          => $unit,
            'price_per_unit'=> $price,
            'region'        => $region,
            'town'          => $town,
            'description'   => $description,
            'harvest_date'  => $harvestDate ?: null,
            'status'        => 'available',
            'image'         => $this->handleImageUpload($farmerId),
        ]);

        Session::setFlash('success', 'Listing added successfully!');
        Auth::redirect('/farmer/listings');
    }

    /* ── Edit listing ────────────────────────────────────────────────── */
    public function editListing(?string $id = null): void {
        $farmerId = Session::userId();
        $listingId = $id ?? ($_GET['id'] ?? null);
        if (!$listingId) Auth::redirect('/farmer/listings');

        $listing = $this->produce->getById((int)$listingId);
        if (!$listing || (int)$listing['farmer_id'] !== $farmerId) {
            Session::setFlash('error', 'Listing not found.');
            Auth::redirect('/farmer/listings');
        }

        $pageTitle = 'Edit Listing';
        $errors = $old = [];
        include BASE_PATH . '/app/views/farmer/edit_listing.php';
    }

    public function doEditListing(): void {
        Auth::requireRole(['farmer']);
        if (!Session::verifyCsrf($_POST['_token'] ?? '')) {
            Session::setFlash('error', 'Invalid request token.');
            Auth::redirect('/farmer/listings');
        }

        $farmerId  = Session::userId();
        $listingId = (int)($_POST['listing_id'] ?? 0);
        $listing   = $this->produce->getById($listingId);

        if (!$listing || (int)$listing['farmer_id'] !== $farmerId) {
            Session::setFlash('error', 'Listing not found.');
            Auth::redirect('/farmer/listings');
        }

        $updateData = [
            'name'          => sanitize($_POST['name'] ?? ''),
            'category'      => sanitize($_POST['category'] ?? ''),
            'quantity'      => (float)($_POST['quantity'] ?? 0),
            'unit'          => sanitize($_POST['unit'] ?? 'kg'),
            'price_per_unit'=> (float)($_POST['price_per_unit'] ?? 0),
            'region'        => sanitize($_POST['region'] ?? ''),
            'town'          => sanitize($_POST['town'] ?? ''),
            'description'   => sanitize($_POST['description'] ?? ''),
            'harvest_date'  => sanitize($_POST['harvest_date'] ?? '') ?: null,
            'status'        => sanitize($_POST['status'] ?? 'available'),
        ];

        // Handle image upload if a new file is provided
        $newImage = $this->handleImageUpload($farmerId);
        if ($newImage) {
            $updateData['image'] = $newImage;
        }

        $this->produce->update($listingId, $updateData);

        Session::setFlash('success', 'Listing updated successfully!');
        Auth::redirect('/farmer/listings');
    }

    public function doDeleteListing(): void {
        Auth::requireRole(['farmer']);
        if (!Session::verifyCsrf($_POST['_token'] ?? '')) {
            Session::setFlash('error', 'Invalid token.'); Auth::redirect('/farmer/listings');
        }
        $farmerId  = Session::userId();
        $listingId = (int)($_POST['listing_id'] ?? 0);
        $listing   = $this->produce->getById($listingId);

        if ($listing && (int)$listing['farmer_id'] === $farmerId) {
            $this->produce->delete($listingId, $farmerId);
            Session::setFlash('success', 'Listing deleted.');
        }
        Auth::redirect('/farmer/listings');
    }

    /* ── Orders ──────────────────────────────────────────────────────── */
    public function orders(): void {
        $farmerId = Session::userId();
        $orders   = $this->order->getBySeller($farmerId);
        $pageTitle = 'Orders Received';
        include BASE_PATH . '/app/views/farmer/orders.php';
    }

    public function doUpdateOrderStatus(): void {
        Auth::requireRole(['farmer']);
        if (!Session::verifyCsrf($_POST['_token'] ?? '')) {
            Session::setFlash('error', 'Invalid token.'); Auth::redirect('/farmer/orders');
        }
        $orderId = (int)($_POST['order_id'] ?? 0);
        $status  = sanitize($_POST['status'] ?? '');
        if ($orderId && $status) {
            $this->order->updateStatus($orderId, $status);

            // Notify the buyer about the status change
            try {
                $order = $this->order->findById($orderId);
                if ($order) {
                    $labels = [
                        'confirmed'  => 'Order Confirmed',
                        'processing' => 'Order Being Prepared',
                        'in_transit' => 'Your Order Is On Its Way',
                        'delivered'  => 'Order Delivered',
                        'cancelled'  => 'Order Cancelled',
                    ];
                    $title = $labels[$status] ?? 'Order Updated';
                    $msg   = "Your order #{$order['order_ref']} has been updated to: " . ucfirst(str_replace('_', ' ', $status)) . '.';
                    $this->notif->create($order['buyer_id'], 'order_status', $title, $msg, '/buyer/orders');
                }
            } catch (\Exception $e) { /* non-critical */ }
        }
        Session::setFlash('success', 'Order status updated.');
        Auth::redirect('/farmer/orders');
    }

    /* ── Private helpers ─────────────────────────────────────────────── */

    /**
     * Handle a produce image upload from $_FILES['produce_image'].
     * Returns the web path to save in the DB, or null if no file uploaded.
     */
    private function handleImageUpload(int $farmerId): ?string {
        if (empty($_FILES['produce_image']['name'])) return null;
        $file = $_FILES['produce_image'];
        if ($file['error'] !== UPLOAD_ERR_OK) return null;

        // Validate MIME type via finfo (not the user-supplied type)
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($mimeType, $allowed, true)) return null;
        if ($file['size'] > 2 * 1024 * 1024) return null; // 2 MB max

        $ext      = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mimeType];
        $filename = 'produce_' . $farmerId . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $dir      = BASE_PATH . '/public/uploads/produce/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        if (move_uploaded_file($file['tmp_name'], $dir . $filename)) {
            return '/uploads/produce/' . $filename;
        }
        return null;
    }

    /* ── Profile ─────────────────────────────────────────────────────── */
    public function profile(): void {
        $user = (new UserModel())->findById(Session::userId());
        $pageTitle = 'My Profile';
        include BASE_PATH . '/app/views/farmer/profile.php';
    }

    public function doUpdateProfile(): void {
        Auth::requireRole(['farmer']);
        if (!Session::verifyCsrf($_POST['_token'] ?? '')) {
            Session::setFlash('error', 'Invalid token.'); Auth::redirect('/farmer/profile');
        }

        $userId  = Session::userId();
        $userMdl = new UserModel();

        $data = [
            'name'   => sanitize($_POST['name']   ?? ''),
            'email'  => filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL),
            'phone'  => sanitize($_POST['phone']  ?? ''),
            'region' => sanitize($_POST['region'] ?? ''),
            'town'   => sanitize($_POST['town']   ?? ''),
        ];

        if (strlen($data['name']) < 2) {
            Session::setFlash('error', 'Name must be at least 2 characters.');
            Auth::redirect('/farmer/profile');
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', 'A valid email address is required.');
            Auth::redirect('/farmer/profile');
        }
        if ($userMdl->emailExistsForOther($data['email'], $userId)) {
            Session::setFlash('error', 'That email address is already in use by another account.');
            Auth::redirect('/farmer/profile');
        }

        $userMdl->update($userId, $data);

        $newPassword = $_POST['password'] ?? '';
        if ($newPassword !== '') {
            if (strlen($newPassword) < 8) {
                Session::setFlash('error', 'Password must be at least 8 characters.');
                Auth::redirect('/farmer/profile');
            }
            $userMdl->changePassword($userId, $newPassword);
        }

        // Refresh session name/region
        $updated = $userMdl->findById($userId);
        $_SESSION['user_name']   = $updated['name'];
        $_SESSION['user_email']  = $updated['email'];
        $_SESSION['user_region'] = $updated['region'] ?? '';

        Session::setFlash('success', 'Profile updated successfully!');
        Auth::redirect('/farmer/profile');
    }
}
