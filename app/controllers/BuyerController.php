<?php
class BuyerController {

    private ProduceModel $produce;
    private OrderModel   $order;
    private BidModel     $bid;
    private NotificationModel $notif;

    public function __construct() {
        Auth::requireRole(['buyer']);
        $this->produce = new ProduceModel();
        $this->order   = new OrderModel();
        $this->bid     = new BidModel();
        $this->notif   = new NotificationModel();
    }

    /* ── Marketplace ──────────────────────────────────────────────── */
    public function marketplace(): void {
        $filters = [
            'region'   => sanitize($_GET['region'] ?? ''),
            'category' => sanitize($_GET['category'] ?? ''),
            'search'   => sanitize($_GET['q'] ?? ''),
            'min_price'    => (float)($_GET['min_price'] ?? 0),
            'max_price'    => (float)($_GET['max_price'] ?? 0),
            'verified_only'=> !empty($_GET['verified_only']),
        ];
        // Always show only available produce in the marketplace
        $listings = $this->produce->search($filters + ['status' => 'available']);

        // "Recommended for you" — top 4 unique produce types from buyer's region
        $user = (new UserModel())->findById(Session::userId());
        $buyerRegion = $user['region'] ?? '';
        $recommended = [];
        if ($buyerRegion) {
            $regionMatches = $this->produce->getAll(['status' => 'available', 'region' => $buyerRegion]);
            $seen = [];
            foreach ($regionMatches as $p) {
                $key = strtolower(trim($p['name']));
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $p['match_score'] = 40;
                    $recommended[] = $p;
                    if (count($recommended) >= 4) break;
                }
            }
        }
        if (empty($recommended)) {
            // fallback: highest rated/newest, deduplicated by name
            $seen = [];
            foreach ($this->produce->getAll(['status' => 'available']) as $p) {
                $key = strtolower(trim($p['name']));
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $recommended[] = $p;
                    if (count($recommended) >= 4) break;
                }
            }
        }

        // Exclude recommended items from the main grid to prevent duplication
        if (!empty($recommended)) {
            $recommendedIds = array_column($recommended, 'id');
            $listings = array_values(array_filter($listings, fn($l) => !in_array($l['id'], $recommendedIds)));
        }

        $pageTitle = 'Marketplace';
        include BASE_PATH . '/app/views/buyer/marketplace.php';
    }

    /* ── Product Detail ──────────────────────────────────────────── */
    public function productDetail(?string $id = null): void {
        $listingId = $id ?? ($_GET['id'] ?? null);
        if (!$listingId) Auth::redirect('/buyer/marketplace');

        $listing = $this->produce->getById((int)$listingId);
        if (!$listing || $listing['status'] !== 'available') {
            Session::setFlash('error', 'Produce listing not found or no longer available.');
            Auth::redirect('/buyer/marketplace');
        }

        $bids    = $this->bid->getByProduce((int)$listingId);
        $pageTitle = e($listing['name']) . ' — Marketplace';
        include BASE_PATH . '/app/views/buyer/product_detail.php';
    }

    /* ── Place Order ─────────────────────────────────────────────── */
    public function doPlaceOrder(): void {
        Auth::requireRole(['buyer']);
        if (!Session::verifyCsrf($_POST['_token'] ?? '')) {
            Session::setFlash('error', 'Invalid request.'); Auth::redirect('/buyer/marketplace');
        }

        $buyerId   = Session::userId();
        $listingId = (int)($_POST['listing_id'] ?? 0);
        $quantity  = (float)($_POST['quantity'] ?? 0);
        $listing   = $this->produce->getById($listingId);

        if (!$listing || $quantity <= 0 || $quantity > $listing['quantity']) {
            Session::setFlash('error', 'Invalid order quantity.');
            Auth::redirect('/buyer/product?id=' . $listingId);
        }

        if ((int)$listing['farmer_id'] === $buyerId) {
            Session::setFlash('error', 'You cannot purchase your own listing.');
            Auth::redirect('/buyer/marketplace');
        }

        $total = $quantity * $listing['price_per_unit'];
        $this->order->create([
            'produce_id'   => $listingId,
            'buyer_id'     => $buyerId,
            'farmer_id'    => $listing['farmer_id'],
            'quantity'     => $quantity,
            'unit'         => $listing['unit'],
            'unit_price'   => $listing['price_per_unit'],
            'total_price'  => $total,
            'status'       => 'pending',
        ]);

        // Notify the farmer
        $buyerName = Session::userName();
        $this->notif->create(
            $listing['farmer_id'], 'order_placed',
            'New Order Received',
            "$buyerName ordered {$quantity} {$listing['unit']} of {$listing['name']}. Review & confirm.",
            '/farmer/orders'
        );

        // Low stock check: if remaining qty ≤ threshold, alert the farmer
        $remaining = $listing['quantity'] - $quantity;
        $threshold = $listing['low_stock_threshold'] ?? 10;
        if ($remaining <= $threshold && $remaining >= 0) {
            $this->notif->create(
                $listing['farmer_id'], 'low_stock',
                'Low Stock Alert — ' . $listing['name'],
                "Your stock of {$listing['name']} is running low ({$remaining} {$listing['unit']} remaining).",
                '/farmer/listings'
            );
        }

        Session::setFlash('success', 'Order placed successfully! The farmer will confirm shortly.');
        Auth::redirect('/buyer/orders');
    }

    /* ── Place Bid ───────────────────────────────────────────────── */
    public function doPlaceBid(): void {
        Auth::requireRole(['buyer']);
        if (!Session::verifyCsrf($_POST['_token'] ?? '')) {
            Session::setFlash('error', 'Invalid request.'); Auth::redirect('/buyer/marketplace');
        }

        $listingId  = (int)($_POST['listing_id'] ?? 0);
        $buyerId    = Session::userId();
        $bidAmount  = (float)($_POST['bid_amount'] ?? 0);
        $quantity   = (float)($_POST['quantity'] ?? 0);
        $message    = sanitize($_POST['message'] ?? '');

        if ($bidAmount <= 0 || $quantity <= 0) {
            Session::setFlash('error', 'Please enter a valid bid amount and quantity.');
            Auth::redirect('/buyer/product?id=' . $listingId);
        }

        $this->bid->create([
            'produce_id' => $listingId,
            'buyer_id'   => $buyerId,
            'bid_price'  => $bidAmount,
            'quantity'   => $quantity,
            'message'    => $message,
        ]);

        Session::setFlash('success', 'Your bid has been submitted to the farmer.');
        Auth::redirect('/buyer/product?id=' . $listingId);
    }

    /* ── My Orders ───────────────────────────────────────────────── */
    public function orders(): void {
        $buyerId = Session::userId();
        $orders  = $this->order->getByBuyer($buyerId);
        $reviewModel = new ReviewModel();
        // Build a set of already-reviewed order IDs for this buyer
        $reviewed = [];
        foreach ($orders as $o) {
            if ($reviewModel->hasReviewed((int)$o['id'], $buyerId)) {
                $reviewed[$o['id']] = true;
            }
        }
        $pageTitle = 'My Orders';
        include BASE_PATH . '/app/views/buyer/orders.php';
    }

    /* ── Write Review ────────────────────────────────────────────── */
    public function showReview(): void {
        $orderId = (int)($_GET['order_id'] ?? 0);
        $buyerId = Session::userId();
        $order   = $this->order->findById($orderId);

        if (!$order || (int)$order['buyer_id'] !== $buyerId || $order['status'] !== 'delivered') {
            Session::setFlash('error', 'Review not available for this order.');
            Auth::redirect('/buyer/orders');
        }

        $reviewModel = new ReviewModel();
        if ($reviewModel->hasReviewed($orderId, $buyerId)) {
            Session::setFlash('info', 'You have already reviewed this order.');
            Auth::redirect('/buyer/orders');
        }

        $pageTitle = 'Write a Review';
        include BASE_PATH . '/app/views/buyer/review.php';
    }

    public function doSubmitReview(): void {
        Auth::requireRole(['buyer']);
        if (!Session::verifyCsrf($_POST['_token'] ?? '')) {
            Session::setFlash('error', 'Invalid request.'); Auth::redirect('/buyer/orders');
        }
        $buyerId = Session::userId();
        $orderId = (int)($_POST['order_id'] ?? 0);
        $rating  = max(1, min(5, (int)($_POST['rating'] ?? 3)));
        $comment = sanitize($_POST['comment'] ?? '');

        $order = $this->order->findById($orderId);
        if (!$order || (int)$order['buyer_id'] !== $buyerId || $order['status'] !== 'delivered') {
            Session::setFlash('error', 'Cannot review this order.');
            Auth::redirect('/buyer/orders');
        }

        $reviewModel = new ReviewModel();
        $reviewModel->create([
            'order_id'    => $orderId,
            'reviewer_id' => $buyerId,
            'reviewee_id' => $order['farmer_id'],
            'produce_id'  => $order['produce_id'],
            'rating'      => $rating,
            'comment'     => $comment,
        ]);

        // Notify the farmer
        $this->notif->create(
            $order['farmer_id'], 'review_received',
            'New Review Received',
            Session::userName() . ' left you a ' . $rating . '-star review for ' . ($order['produce_name'] ?? 'your produce') . '.',
            '/farmer/orders'
        );

        Session::setFlash('success', 'Thank you for your review!');
        Auth::redirect('/buyer/orders');
    }

    /* ── Smart Matching ──────────────────────────────────────────── */
    public function matching(): void {
        $user        = (new UserModel())->findById(Session::userId());
        $buyerRegion = $user['region'] ?? '';

        $category = sanitize($_GET['category'] ?? '');
        $quantity = max(0, (float)($_GET['quantity'] ?? 0));
        $budget   = max(0, (float)($_GET['budget']   ?? 0));

        // Fetch all available produce
        $all = $this->produce->getAll(['status' => 'available']);

        // Score each listing
        foreach ($all as &$p) {
            $score = 0;
            if ($buyerRegion && strcasecmp($p['region'], $buyerRegion) === 0) $score += 40;
            if ($category    && $p['category'] === $category)                 $score += 30;
            if ($quantity > 0 && $p['quantity'] >= $quantity)                 $score += 20;
            if ($budget > 0   && $p['price_per_unit'] <= $budget)             $score += 10;

            $p['match_score'] = $score;
            if ($score >= 80)      { $p['match_label'] = 'High Match';     $p['score_class'] = 'emerald'; }
            elseif ($score >= 50)  { $p['match_label'] = 'Good Match';     $p['score_class'] = 'green';   }
            elseif ($score >= 20)  { $p['match_label'] = 'Moderate Match'; $p['score_class'] = 'amber';   }
            else                   { $p['match_label'] = 'Low Match';      $p['score_class'] = 'slate';   }
        }
        unset($p);

        // Sort by score desc, then by price asc
        usort($all, fn($a, $b) => $b['match_score'] <=> $a['match_score'] ?: $a['price_per_unit'] <=> $b['price_per_unit']);

        // Regional demand: count available produce per region
        $regionCounts = [];
        foreach ($all as $item) {
            $r = $item['region'] ?? 'Unknown';
            if (!isset($regionCounts[$r])) $regionCounts[$r] = ['count' => 0, 'value' => 0];
            $regionCounts[$r]['count']++;
            $regionCounts[$r]['value'] += $item['price_per_unit'] * $item['quantity'];
        }
        arsort($regionCounts);

        $categories = ['tubers','cereals','legumes','vegetables','fruits','cash_crops','other'];
        $pageTitle   = 'Smart Supply-Demand Matching';
        include BASE_PATH . '/app/views/buyer/matching.php';
    }
}
