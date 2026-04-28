<?php
/**
 * AgriLink – Utility helpers
 */

class Helpers {

    /** Safely escape output. */
    public static function e(mixed $value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** Format price in Ghana Cedis. */
    public static function money(float $amount): string {
        return CURRENCY_SYMBOL . number_format($amount, 2);
    }

    /** Format date in human-readable form. */
    public static function date(string $datetime, string $format = 'M j, Y'): string {
        if (empty($datetime)) return '—';
        return date($format, strtotime($datetime));
    }

    /** Generate a cryptographically random order reference like AL-A3F2B1. */
    public static function generateOrderRef(): string {
        return 'AL-' . strtoupper(bin2hex(random_bytes(3)));
    }

    /** Status badge HTML. */
    public static function statusBadge(string $status): string {
        $map = [
            'available'   => ['bg-green-100 text-green-800',   'Available'],
            'reserved'    => ['bg-yellow-100 text-yellow-800', 'Reserved'],
            'sold'        => ['bg-gray-100 text-gray-600',     'Sold'],
            'archived'    => ['bg-red-100 text-red-700',       'Archived'],
            'pending'     => ['bg-yellow-100 text-yellow-800', 'Pending'],
            'confirmed'   => ['bg-blue-100 text-blue-800',     'Confirmed'],
            'processing'  => ['bg-purple-100 text-purple-800', 'Processing'],
            'in_transit'  => ['bg-cyan-100 text-cyan-800',     'In Transit'],
            'delivered'   => ['bg-green-100 text-green-800',   'Delivered'],
            'cancelled'   => ['bg-red-100 text-red-700',       'Cancelled'],
            'assigned'    => ['bg-blue-100 text-blue-800',     'Assigned'],
            'failed'      => ['bg-red-100 text-red-700',       'Failed'],
        ];
        [$classes, $label] = $map[$status] ?? ['bg-gray-100 text-gray-600', ucfirst($status)];
        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold ' . $classes . '">' . self::e($label) . '</span>';
    }

    /** Redirect helper. */
    public static function redirect(string $url): never {
        header('Location: ' . $url);
        exit;
    }

    /** Simple matching score (supply/demand). */
    public static function matchScore(array $produce, string $buyerRegion, string $category): int {
        $score = 0;
        if (strcasecmp($produce['region'], $buyerRegion) === 0) $score += 40;
        if (strcasecmp($produce['category'], $category) === 0)  $score += 30;
        if ($produce['quantity'] >= 10)                          $score += 20;
        if ($produce['price_per_unit'] <= 200)                   $score += 10;
        return $score;
    }

    /** Paginate an array. Returns [items, totalPages]. */
    public static function paginate(array $items, int $page, int $perPage = 10): array {
        $total = count($items);
        $pages = max(1, (int)ceil($total / $perPage));
        $slice = array_slice($items, ($page - 1) * $perPage, $perPage);
        return [$slice, $pages, $total];
    }

    /** Strip tags and trim input. */
    public static function sanitize(string $input): string {
        return trim(strip_tags($input));
    }

    /**
     * Return a relevant produce image URL.
     * Falls back to a generic farm-harvest photo.
     */
    public static function produceImage(string $name, string $category = '', int $w = 400, int $q = 80): string {
        $text = strtolower($name . ' ' . $category);
        // Only confirmed-working Unsplash IDs are used below (verified from live site).
        // IDs that returned ERR_BLOCKED_BY_ORB (invalid photo) have been replaced.
        // IMPORTANT: Longer/compound keywords MUST come before shorter ones to avoid
        // substring false-matches (e.g. "cocoyam" contains "yam", "cowpea" could match "pea").
        $map = [
            // --- compound keywords first ---
            'kontomire'  => 'https://images.pexels.com/photos/31579980/pexels-photo-31579980.jpeg?auto=compress&cs=tinysrgb&w={w}',  // cocoyam/taro leaves ✓
            'cocoyam'    => 'https://images.pexels.com/photos/31579980/pexels-photo-31579980.jpeg?auto=compress&cs=tinysrgb&w={w}',  // taro/cocoyam plant ✓
            'taro'       => 'https://images.pexels.com/photos/31579980/pexels-photo-31579980.jpeg?auto=compress&cs=tinysrgb&w={w}',  // taro leaves ✓
            'watermelon' => 'https://images.pexels.com/photos/1313267/pexels-photo-1313267.jpeg?auto=compress&cs=tinysrgb&w={w}',  // watermelon ✓
            'pineapple'  => '1550258987-190a2d41a8ba',  // pineapple ✓
            'plantain'   => '1667308888281-8030a5f827c5',  // plantain/banana tree ✓
            'groundnut'  => 'https://images.pexels.com/photos/209371/pexels-photo-209371.jpeg?auto=compress&cs=tinysrgb&w={w}',  // peanuts/groundnuts ✓
            'sorghum'    => '1665904285523-47c0a6fdfc0e',  // Ghana market ✓
            'cassava'    => 'https://images.pexels.com/photos/30893342/pexels-photo-30893342.jpeg?auto=compress&cs=tinysrgb&w={w}',  // sliced cassava tubers ✓
            'cowpea'     => 'https://images.pexels.com/photos/3671651/pexels-photo-3671651.jpeg?auto=compress&cs=tinysrgb&w={w}',  // cowpea bean pods ✓
            // --- simple keywords after ---
            'tomato'     => 'https://images.pexels.com/photos/1327838/pexels-photo-1327838.jpeg?auto=compress&cs=tinysrgb&w={w}',  // vine tomatoes ✓
            'cocoa'      => 'https://images.pexels.com/photos/7543127/pexels-photo-7543127.jpeg?auto=compress&cs=tinysrgb&w={w}',  // cocoa pods ✓
            'maize'      => 'https://images.pexels.com/photos/547263/pexels-photo-547263.jpeg?auto=compress&cs=tinysrgb&w={w}',  // corn cobs ✓
            'corn'       => 'https://images.pexels.com/photos/547263/pexels-photo-547263.jpeg?auto=compress&cs=tinysrgb&w={w}',  // corn cobs ✓
            'rice'       => 'https://images.pexels.com/photos/35072278/pexels-photo-35072278.jpeg?auto=compress&cs=tinysrgb&w={w}',  // rice harvest field ✓
            'banana'     => '1667308888281-8030a5f827c5',  // plantain/banana tree ✓
            'pepper'     => '1665904285523-47c0a6fdfc0e',  // chilli vendor — Ghana market ✓
            'chili'      => '1665904285523-47c0a6fdfc0e',  // Ghana chili market ✓
            'ginger'     => 'https://images.pexels.com/photos/10112136/pexels-photo-10112136.jpeg?auto=compress&cs=tinysrgb&w={w}',  // ginger roots ✓
            'mango'      => 'https://images.pexels.com/photos/2935021/pexels-photo-2935021.jpeg?auto=compress&cs=tinysrgb&w={w}',  // mango on tree ✓
            'peanut'     => 'https://images.pexels.com/photos/209371/pexels-photo-209371.jpeg?auto=compress&cs=tinysrgb&w={w}',  // peanuts ✓
            'shea'       => 'https://images.pexels.com/photos/12392906/pexels-photo-12392906.jpeg?auto=compress&cs=tinysrgb&w={w}',  // shea nuts/kernels ✓
            'onion'      => '1665904285523-47c0a6fdfc0e',  // Ghana market ✓
            'palm'       => 'https://images.pexels.com/photos/35536889/pexels-photo-35536889.jpeg?auto=compress&cs=tinysrgb&w={w}',  // palm tree with fruit ✓
            'bean'       => 'https://images.pexels.com/photos/3671651/pexels-photo-3671651.jpeg?auto=compress&cs=tinysrgb&w={w}',  // bean pods ✓
            'legume'     => 'https://images.pexels.com/photos/3671651/pexels-photo-3671651.jpeg?auto=compress&cs=tinysrgb&w={w}',  // legume pods ✓
            'yam'        => '1776153380872-108ba14dc63d',  // Kumasi market ✓
            'vegetable'  => '1665904285523-47c0a6fdfc0e',  // Ghana market ✓
            'fruit'      => 'https://images.pexels.com/photos/2935021/pexels-photo-2935021.jpeg?auto=compress&cs=tinysrgb&w={w}',  // tropical fruit ✓
        ];
        $asset = '1776153380872-108ba14dc63d'; // default: Kumasi marketplace, Ghana ✓
        foreach ($map as $keyword => $photoId) {
            if (str_contains($text, $keyword)) {
                $asset = $photoId;
                break;
            }
        }
        if (str_starts_with($asset, 'http')) {
            return str_replace(['{w}', '{q}'], [(string)$w, (string)$q], $asset);
        }
        return "https://images.unsplash.com/photo-{$asset}?auto=format&fit=crop&w={$w}&q={$q}";
    }
}

// Global convenience wrappers — used throughout controllers and views
function e(mixed $val): string       { return Helpers::e($val); }
function sanitize(string $s): string { return Helpers::sanitize($s); }
