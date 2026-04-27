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
}

// Global convenience wrappers — used throughout controllers and views
function e(mixed $val): string       { return Helpers::e($val); }
function sanitize(string $s): string { return Helpers::sanitize($s); }
