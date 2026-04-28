<?php
/**
 * AgriLink Ghana — Unit Test Runner
 * CS 415 Software Engineering Final Project
 *
 * Run with: php tests/run_tests.php
 * (from the agrilink/ project root)
 */

define('BASE_PATH', __DIR__ . '/..');

// ── Bootstrap: set SERVER defaults for CLI, then load real config ──────────
$_SERVER['REMOTE_ADDR']  = $_SERVER['REMOTE_ADDR']  ?? '127.0.0.1';
$_SERVER['SERVER_NAME']  = $_SERVER['SERVER_NAME']  ?? 'localhost';
$_SERVER['REQUEST_URI']  = $_SERVER['REQUEST_URI']  ?? '/';
$_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';

require_once BASE_PATH . '/app/config/config.php';

// ── Load only the classes under test (no DB, no controller needed) ─────────
require_once BASE_PATH . '/app/core/Helpers.php';

// ── Tiny test framework ────────────────────────────────────────────────────
$passed = 0;
$failed = 0;
$results = [];

function assert_equals(string $label, mixed $expected, mixed $actual): void {
    global $passed, $failed, $results;
    if ($expected === $actual) {
        $passed++;
        $results[] = "[PASS] $label";
    } else {
        $failed++;
        $results[] = "[FAIL] $label\n        Expected: " . var_export($expected, true)
                   . "\n        Actual:   " . var_export($actual, true);
    }
}

function assert_contains(string $label, string $needle, string $haystack): void {
    global $passed, $failed, $results;
    if (str_contains($haystack, $needle)) {
        $passed++;
        $results[] = "[PASS] $label";
    } else {
        $failed++;
        $results[] = "[FAIL] $label\n        Expected to contain: '$needle'\n        Actual: '$haystack'";
    }
}

function assert_starts_with(string $label, string $prefix, string $actual): void {
    global $passed, $failed, $results;
    if (str_starts_with($actual, $prefix)) {
        $passed++;
        $results[] = "[PASS] $label";
    } else {
        $failed++;
        $results[] = "[FAIL] $label\n        Expected to start with: '$prefix'\n        Actual: '$actual'";
    }
}

function assert_true(string $label, bool $condition): void {
    global $passed, $failed, $results;
    if ($condition) {
        $passed++;
        $results[] = "[PASS] $label";
    } else {
        $failed++;
        $results[] = "[FAIL] $label (condition was false)";
    }
}

function assert_count(string $label, int $expected, array $actual): void {
    global $passed, $failed, $results;
    $c = count($actual);
    if ($c === $expected) {
        $passed++;
        $results[] = "[PASS] $label";
    } else {
        $failed++;
        $results[] = "[FAIL] $label\n        Expected count: $expected, Actual count: $c";
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// TEST SUITE 1: Helpers::e() — XSS output escaping
// ═══════════════════════════════════════════════════════════════════════════

assert_equals(
    "Helpers::e() escapes HTML special characters",
    '&lt;script&gt;alert(&#039;xss&#039;)&lt;/script&gt;',
    Helpers::e("<script>alert('xss')</script>")
);

assert_equals(
    "Helpers::e() escapes double quotes",
    '&quot;hello&quot;',
    Helpers::e('"hello"')
);

assert_equals(
    "Helpers::e() returns empty string for null",
    '',
    Helpers::e(null)
);

assert_equals(
    "Helpers::e() returns plain string unchanged",
    'Hello Ghana',
    Helpers::e('Hello Ghana')
);

assert_equals(
    "Helpers::e() escapes ampersand",
    'Farmers &amp; Buyers',
    Helpers::e('Farmers & Buyers')
);

// ═══════════════════════════════════════════════════════════════════════════
// TEST SUITE 2: Helpers::money() — Currency formatting
// ═══════════════════════════════════════════════════════════════════════════

// Use the actual CURRENCY_SYMBOL constant so the test is encoding-agnostic.
assert_equals(
    "Helpers::money() formats integer — number part correct",
    CURRENCY_SYMBOL . '100.00',
    Helpers::money(100)
);

assert_equals(
    "Helpers::money() formats decimal correctly",
    CURRENCY_SYMBOL . '450.75',
    Helpers::money(450.75)
);

assert_equals(
    "Helpers::money() formats zero",
    CURRENCY_SYMBOL . '0.00',
    Helpers::money(0)
);

assert_equals(
    "Helpers::money() uses thousands separator for large amounts",
    CURRENCY_SYMBOL . '12,000.00',
    Helpers::money(12000)
);

// ═══════════════════════════════════════════════════════════════════════════
// TEST SUITE 3: Helpers::generateOrderRef() — Unique reference generation
// ═══════════════════════════════════════════════════════════════════════════

$ref1 = Helpers::generateOrderRef();
$ref2 = Helpers::generateOrderRef();

assert_starts_with(
    "Helpers::generateOrderRef() starts with 'AL-'",
    'AL-',
    $ref1
);

assert_equals(
    "Helpers::generateOrderRef() is 9 characters long (AL- + 6 hex chars)",
    9,
    strlen($ref1)
);

assert_true(
    "Helpers::generateOrderRef() generates unique references",
    $ref1 !== $ref2
);

assert_true(
    "Helpers::generateOrderRef() is uppercase",
    $ref1 === strtoupper($ref1)
);

// ═══════════════════════════════════════════════════════════════════════════
// TEST SUITE 4: Helpers::paginate() — Pagination logic
// ═══════════════════════════════════════════════════════════════════════════

$items = range(1, 25); // 25 items

[$page1Items, $totalPages, $total] = Helpers::paginate($items, 1, 10);
assert_count("Helpers::paginate() returns 10 items for page 1 of 25", 10, $page1Items);
assert_equals("Helpers::paginate() calculates 3 total pages for 25 items", 3, $totalPages);
assert_equals("Helpers::paginate() returns correct total count", 25, $total);

[$page2Items] = Helpers::paginate($items, 2, 10);
assert_equals("Helpers::paginate() page 2 starts at item 11", 11, $page2Items[0]);

[$page3Items] = Helpers::paginate($items, 3, 10);
assert_count("Helpers::paginate() page 3 returns 5 items (remainder)", 5, $page3Items);

[$emptyItems, $emptyPages] = Helpers::paginate([], 1, 10);
assert_count("Helpers::paginate() handles empty array", 0, $emptyItems);
assert_equals("Helpers::paginate() empty array returns 1 total page", 1, $emptyPages);

// ═══════════════════════════════════════════════════════════════════════════
// TEST SUITE 5: Helpers::matchScore() — Supply/demand matching algorithm
// ═══════════════════════════════════════════════════════════════════════════

// Use isolated data for each dimension test.
// Produce with low qty (<10) and high price (>200) zeroes out those bonus points.
$produceIsolated = [
    'region'         => 'Ashanti',
    'category'       => 'cereals',
    'quantity'       => 5,   // < 10, so +0 for quantity
    'price_per_unit' => 250, // > 200, so +0 for price
];

assert_equals(
    "Helpers::matchScore() gives 40 for matching region only",
    40,
    Helpers::matchScore($produceIsolated, 'Ashanti', 'vegetables')
);

assert_equals(
    "Helpers::matchScore() gives 30 for matching category only",
    30,
    Helpers::matchScore($produceIsolated, 'Greater Accra', 'cereals')
);

assert_equals(
    "Helpers::matchScore() gives 20 for quantity >= 10 only",
    20,
    Helpers::matchScore(['region'=>'X','category'=>'X','quantity'=>50,'price_per_unit'=>500], 'Y', 'Z')
);

$produceFull = [
    'region'         => 'Ashanti',
    'category'       => 'cereals',
    'quantity'       => 50,  // >= 10: +20
    'price_per_unit' => 150, // <= 200: +10
];
assert_equals(
    "Helpers::matchScore() gives 100 for region + category + quantity + price match",
    100,
    Helpers::matchScore($produceFull, 'Ashanti', 'cereals')
);

assert_equals(
    "Helpers::matchScore() gives 0 for no match",
    0,
    Helpers::matchScore(['region'=>'X','category'=>'X','quantity'=>5,'price_per_unit'=>500], 'Y', 'Z')
);

// ═══════════════════════════════════════════════════════════════════════════
// TEST SUITE 6: Helpers::statusBadge() — Factory method
// ═══════════════════════════════════════════════════════════════════════════

assert_contains("Helpers::statusBadge() contains 'Pending' label",        'Pending',    Helpers::statusBadge('pending'));
assert_contains("Helpers::statusBadge() contains 'Available' label",      'Available',  Helpers::statusBadge('available'));
assert_contains("Helpers::statusBadge() contains 'Delivered' label",      'Delivered',  Helpers::statusBadge('delivered'));
assert_contains("Helpers::statusBadge() contains 'In Transit' label",     'In Transit', Helpers::statusBadge('in_transit'));
assert_contains("Helpers::statusBadge() contains 'Cancelled' label",      'Cancelled',  Helpers::statusBadge('cancelled'));
assert_contains("Helpers::statusBadge() contains 'span' HTML tag",        '<span',      Helpers::statusBadge('pending'));

// Unknown status should gracefully return capitalised key
$unknown = Helpers::statusBadge('disputed');
assert_contains("Helpers::statusBadge() handles unknown status gracefully", 'Disputed', $unknown);

// ═══════════════════════════════════════════════════════════════════════════
// TEST SUITE 7: Helpers::sanitize() — Input sanitisation
// ═══════════════════════════════════════════════════════════════════════════

assert_equals(
    "Helpers::sanitize() strips HTML tags",
    'hello world',
    Helpers::sanitize('<b>hello</b> world')
);

assert_equals(
    "Helpers::sanitize() trims leading/trailing whitespace",
    'Fresh Yam',
    Helpers::sanitize('  Fresh Yam  ')
);

assert_equals(
    "Helpers::sanitize() strips script tags",
    "alert('xss')",
    Helpers::sanitize("<script>alert('xss')</script>")
);

assert_equals(
    "Helpers::sanitize() leaves plain text unchanged",
    'Kontomire',
    Helpers::sanitize('Kontomire')
);

// ═══════════════════════════════════════════════════════════════════════════
// TEST SUITE 8: Helpers::produceImage() — Keyword-to-image mapping
// Critical: compound keywords must match before shorter substrings
// ═══════════════════════════════════════════════════════════════════════════

// Kontomire contains 'yam' as substring in 'cocoyam' — must still map to taro photo
$kontomireUrl = Helpers::produceImage('Kontomire (Cocoyam Leaves)', 'vegetables');
assert_contains(
    "produceImage() maps 'Kontomire (Cocoyam Leaves)' to taro/cocoyam Pexels photo (not yam)",
    'pexels-photo-31579980',
    $kontomireUrl
);

// Cocoyam contains 'yam' as substring — must match 'cocoyam' keyword first
$cocoyamUrl = Helpers::produceImage('Cocoyam', 'tubers');
assert_contains(
    "produceImage() maps 'Cocoyam' to taro photo (substring-safe: 'cocoyam' before 'yam')",
    'pexels-photo-31579980',
    $cocoyamUrl
);

// Cowpea contains 'pea' as substring — must match 'cowpea' first
$cowpeaUrl = Helpers::produceImage('Cowpea', 'legumes');
assert_contains(
    "produceImage() maps 'Cowpea' to cowpea bean photo (substring-safe: 'cowpea' before 'pea'/'bean')",
    'pexels-photo-3671651',
    $cowpeaUrl
);

// Direct yam should map to yam photo
$yamUrl = Helpers::produceImage('Yam', 'tubers');
assert_contains(
    "produceImage() maps 'Yam' to Unsplash yam/market photo",
    'images.unsplash.com',
    $yamUrl
);

// Groundnut maps to peanut pile photo
$groundnutUrl = Helpers::produceImage('Groundnut', 'legumes');
assert_contains(
    "produceImage() maps 'Groundnut' to peanuts Pexels photo",
    'pexels-photo-209371',
    $groundnutUrl
);

// Unknown produce falls back to default (use a name with no matching keyword)
$unknownUrl = Helpers::produceImage('Baobab Seed Powder', 'other');
assert_contains(
    "produceImage() falls back to default Unsplash photo for unknown produce",
    'images.unsplash.com',
    $unknownUrl
);

// URL is a valid CDN URL
assert_starts_with(
    "produceImage() always returns a full URL starting with https://",
    'https://',
    Helpers::produceImage('Maize', 'cereals')
);

// ═══════════════════════════════════════════════════════════════════════════
// OUTPUT RESULTS
// ═══════════════════════════════════════════════════════════════════════════

$total = $passed + $failed;
$border = str_repeat('═', 66);

echo PHP_EOL;
echo "AgriLink Ghana — Unit Test Suite" . PHP_EOL;
echo "CS 415 Software Engineering | Final Project" . PHP_EOL;
echo $border . PHP_EOL;

foreach ($results as $line) {
    echo $line . PHP_EOL;
}

echo $border . PHP_EOL;

if ($failed === 0) {
    echo "Results: {$passed}/{$total} passed  ✓  All tests PASS" . PHP_EOL;
} else {
    echo "Results: {$passed}/{$total} passed, {$failed} FAILED" . PHP_EOL;
    exit(1);
}

echo PHP_EOL;
