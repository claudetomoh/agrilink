# AgriLink Ghana — Software Design Patterns

**CS 415: Software Engineering | Final Project Documentation**  
**Module: Engineering Quality & Architecture**

---

## Overview

AgriLink Ghana is built on a deliberate application of well-established software design patterns sourced from the Gang of Four (GoF) catalogue, enterprise application architecture, and domain-driven design. This document catalogues every pattern applied in the codebase, explains the engineering problem it solves, and links each pattern to its concrete implementation in the project source.

Patterns are grouped by category: Architectural, Creational, Structural, and Behavioral.

---

## Table of Contents

1. [MVC — Model-View-Controller](#1-mvc--model-view-controller)
2. [Front Controller](#2-front-controller)
3. [Layered Architecture](#3-layered-architecture)
4. [Repository Pattern](#4-repository-pattern)
5. [Singleton Pattern](#5-singleton-pattern)
6. [Facade Pattern](#6-facade-pattern)
7. [Strategy Pattern](#7-strategy-pattern)
8. [Observer Pattern](#8-observer-pattern)
9. [Template Method / Composite View](#9-template-method--composite-view)
10. [Specification / Filter Builder](#10-specification--filter-builder)
11. [Chain of Responsibility](#11-chain-of-responsibility)
12. [Factory Method](#12-factory-method)
13. [Summary Table](#13-summary-table)

---

## 1. MVC — Model-View-Controller

**Category:** Architectural  
**GoF Reference:** Compound pattern (not directly in GoF but derived from Observer + Strategy)

### Problem

Without separation of concerns, business logic, data access, and UI rendering become entangled. Changes to the database affect the UI; changes to the UI affect business logic. This makes the code fragile, hard to test, and difficult to maintain.

### Solution

The entire AgriLink application is structured around MVC. Each layer has a single, well-defined responsibility:

| Layer | Responsibility | Location |
|---|---|---|
| **Model** | Data access, business rules, validation | `app/models/` |
| **View** | Presentation logic only — renders data from controller | `app/views/` |
| **Controller** | Orchestrates user request → model calls → view render | `app/controllers/` |

### Implementation

```
app/
├── models/
│   ├── UserModel.php        ← data access for users
│   ├── ProduceModel.php     ← data access for listings
│   ├── OrderModel.php       ← data access for orders
│   └── NotificationModel.php
├── controllers/
│   ├── FarmerController.php ← handles farmer requests
│   ├── BuyerController.php  ← handles buyer requests
│   └── AdminController.php
└── views/
    ├── farmer/dashboard.php ← renders farmer dashboard HTML
    └── buyer/marketplace.php
```

**Controller example — FarmerController.php:**

```php
public function dashboard(): void {
    $farmerId = Session::userId();
    $listings = $this->produce->getByFarmer($farmerId);  // ← Model call
    $orders   = $this->order->getBySeller($farmerId);    // ← Model call

    $stats = [
        'active_listings' => count(array_filter($listings, fn($l) => $l['status'] === 'available')),
        'total_revenue'   => array_sum(array_column(
            array_filter($orders, fn($o) => in_array($o['status'], ['delivered', 'completed'])),
            'total_price'
        )),
    ];

    include BASE_PATH . '/app/views/farmer/dashboard.php'; // ← View
}
```

The controller does not contain SQL. The view does not contain business logic. This clean boundary is maintained consistently across all eight controllers.

### Benefit

- Models, Views, and Controllers can be changed independently.
- Views can be swapped (e.g., a JSON API response) without touching the model.
- Controllers and models can be unit tested without rendering HTML.

---

## 2. Front Controller

**Category:** Architectural  
**GoF Reference:** Equivalent to Command/Chain pattern applied at the routing layer

### Problem

In a naive PHP application, each URL maps to a separate PHP file. This leads to duplicated security checks, inconsistent session handling, and no central place to apply global rules.

### Solution

AgriLink uses a single entry point: `public/index.php`. Every HTTP request — regardless of URL path — is routed through this single file. It bootstraps configuration, autoloads classes, starts sessions, and delegates to the appropriate controller.

**File: `public/index.php`**

```php
// Every request enters here
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/config/config.php';
require_once BASE_PATH . '/app/config/database.php';
require_once BASE_PATH . '/app/core/Session.php';
require_once BASE_PATH . '/app/core/Auth.php';

// Autoload all models and controllers
foreach (glob(BASE_PATH . '/app/models/*.php') as $model)  { require_once $model; }
foreach (glob(BASE_PATH . '/app/controllers/*.php') as $ctrl) { require_once $ctrl; }

Session::start();

// Parse the URL
$url      = trim($_GET['url'] ?? 'home', '/');
$segments = explode('/', $url);

// Route table — maps URL to [Controller, method]
$routes = [
    'farmer/dashboard' => ['FarmerController', 'dashboard'],
    'buyer/marketplace'=> ['BuyerController',  'marketplace'],
    'admin/users'      => ['AdminController',  'users'],
    // ... all other routes
];

// Dispatch
$key = implode('/', array_slice($segments, 0, 2));
[$ctrlClass, $method] = $routes[$key] ?? ['HomeController', 'index'];
(new $ctrlClass())->$method($segments[2] ?? null);
```

The `.htaccess` Apache rewrite rule funnels all requests:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
```

### Benefit

- Security headers, session management, and config loading happen once, centrally.
- Adding a new route is a single-line change in one file.
- No risk of a developer forgetting to call `Auth::require()` on a forgotten entry file.

---

## 3. Layered Architecture

**Category:** Architectural

### Problem

Without defined layers, developers mix SQL queries into view files, or write presentation logic inside models. The result is a "big ball of mud" — impossible to maintain or test.

### Solution

AgriLink enforces a strict three-tier layered architecture. Dependencies only flow **downward** — controllers may call models, but models never call controllers. Views never contain SQL.

```
┌────────────────────────────────────────────┐
│           PRESENTATION LAYER               │
│  Views (PHP/HTML), Partials, CSS/JS        │
│  app/views/**, public/css, public/js       │
└────────────────────┬───────────────────────┘
                     │ (rendered by)
┌────────────────────▼───────────────────────┐
│            APPLICATION LAYER               │
│  Controllers — orchestrate requests        │
│  app/controllers/**Controller.php          │
│  Core — Auth, Session, Helpers             │
│  app/core/Auth.php, Session.php            │
└────────────────────┬───────────────────────┘
                     │ (calls)
┌────────────────────▼───────────────────────┐
│              DATA LAYER                    │
│  Models — data access, business rules      │
│  app/models/**Model.php                    │
│  Database — PDO singleton                  │
│  app/config/database.php                   │
└────────────────────────────────────────────┘
```

Each layer is enforced by convention and structure. The `public/` directory (webroot) contains only `index.php`, CSS, JS, and uploads — no business logic is reachable directly.

### Benefit

- Swapping the database (e.g., MySQL → PostgreSQL) only touches the Data Layer.
- Redesigning the UI only touches the Presentation Layer.
- Application rules (auth, validation) stay in the Application Layer.

---

## 4. Repository Pattern

**Category:** Architectural (Data Access)  
**GoF Reference:** Derived from Domain-Driven Design; related to Gateway pattern

### Problem

If controllers contain raw SQL, every change to the database schema requires hunting through all controllers. Business logic becomes coupled to the persistence mechanism.

### Solution

Each domain entity has a dedicated Model class that acts as its repository — a single object responsible for all data access for that entity. Controllers never write SQL; they call methods on the model.

**File: `app/models/ProduceModel.php` (excerpt)**

```php
class ProduceModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect(); // ← Singleton injected
    }

    public function getAll(array $filters = []): array { /* ... */ }
    public function findById(int $id): array|false      { /* ... */ }
    public function create(array $data): int            { /* ... */ }
    public function update(int $id, array $data): bool  { /* ... */ }
    public function delete(int $id): bool               { /* ... */ }
    public function getByFarmer(int $farmerId): array   { /* ... */ }
    public function getLowStock(int $farmerId): array   { /* ... */ }
    public function getRegionalDemand(string $region, int $limit): array { /* ... */ }
}
```

**Controller usage (no SQL visible):**

```php
// FarmerController.php
$listings = $this->produce->getByFarmer($farmerId);
$lowStock = $this->produce->getLowStock($farmerId);
```

AgriLink has seven repositories:

| Repository | Entity |
|---|---|
| `UserModel` | User accounts |
| `ProduceModel` | Produce listings |
| `OrderModel` | Orders |
| `BidModel` | Price bids |
| `DeliveryModel` | Deliveries |
| `ReviewModel` | Farmer reviews |
| `NotificationModel` | In-app notifications |

### Benefit

- The persistence mechanism (SQL, queries, table names) is hidden behind a clean API.
- All queries for a domain object are in one file — easy to audit, optimize, or change.
- Mocking models for testing is straightforward.

---

## 5. Singleton Pattern

**Category:** Creational  
**GoF Reference:** Singleton (GoF, p. 127)

### Problem

Creating a new database connection for every query is expensive. An application making 20 queries per page request would open 20 separate MySQL connections, exhausting the server connection pool rapidly.

### Solution

`Database` uses the Singleton pattern to ensure only one PDO connection instance exists per request lifecycle. All models share this single connection.

**File: `app/config/database.php`**

```php
class Database {
    private static ?PDO $instance = null;   // ← single stored instance

    public static function connect(): PDO {
        if (self::$instance === null) {     // ← only created once
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
            );
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
        }
        return self::$instance;             // ← same instance every call
    }
}
```

**Usage across all seven models:**

```php
// UserModel.php
$this->db = Database::connect(); // ← shared instance

// ProduceModel.php
$this->db = Database::connect(); // ← same instance, no new connection

// OrderModel.php
$this->db = Database::connect(); // ← same instance
```

### Benefit

- One connection per request — optimal resource use.
- Credentials are configured once; not scattered across model constructors.
- Connection errors are handled centrally in one place.

---

## 6. Facade Pattern

**Category:** Structural  
**GoF Reference:** Facade (GoF, p. 185)

### Problem

Session management, authentication, and utility operations involve complex, repetitive code. Every controller would need to know how `$_SESSION` works, how headers are set for redirects, and how to escape HTML output.

### Solution

AgriLink provides three Facade classes that wrap complex subsystems behind a simple, readable interface.

### Auth Facade — `app/core/Auth.php`

Wraps HTTP header manipulation, session checks, and role enforcement:

```php
class Auth {
    public static function require(): void {
        Session::start();
        if (!Session::isLoggedIn()) {
            Session::flash('error', 'Please log in to access that page.');
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }

    public static function requireRole(string|array $roles): void {
        self::require();  // ← delegates to Auth::require()
        if (!in_array(Session::userRole(), (array)$roles, true)) {
            http_response_code(403);
            include APP_ROOT . '/app/views/partials/403.php';
            exit;
        }
    }

    public static function redirectToDashboard(): void {
        $url = match(Session::userRole()) {
            'farmer'    => APP_URL . '/farmer/dashboard',
            'buyer'     => APP_URL . '/buyer/marketplace',
            'transport' => APP_URL . '/transport/dashboard',
            'admin'     => APP_URL . '/admin/dashboard',
            default     => APP_URL . '/login',
        };
        header('Location: ' . $url);
        exit;
    }
}
```

**Controller usage (three words, not thirty):**

```php
// Instead of 15 lines of session/header code:
Auth::requireRole(['farmer']);
```

### Session Facade — `app/core/Session.php`

Wraps PHP's `$_SESSION` superglobal with safe, consistent methods:

```php
// Instead of: if (isset($_SESSION['user_id'])) ...
if (Session::isLoggedIn()) { ... }

// Instead of: $_SESSION['flash_error'] = 'message';
Session::flash('error', 'Invalid credentials.');

// CSRF — generates and verifies tokens transparently
$token = Session::csrfToken();       // generate
Session::verifyCsrf($_POST['_token']); // verify
```

### Helpers Facade — `app/core/Helpers.php`

Centralises utility operations used across all views:

```php
// XSS-safe output
echo Helpers::e($user['name']);

// Format currency in Ghana Cedis
echo Helpers::money($order['total_price']); // → ₵ 450.00

// Status badge HTML with colour coding
echo Helpers::statusBadge($order['status']); // → <span class="...">Pending</span>

// Matching score algorithm
$score = Helpers::matchScore($produce, $buyerRegion, $category);
```

### Benefit

- Calling code is clean and expressive.
- Security-critical operations (CSRF, XSS escaping) are centralised — one fix fixes all.
- The underlying complexity of `$_SESSION`, `header()`, `htmlspecialchars()` is hidden.

---

## 7. Strategy Pattern

**Category:** Behavioral  
**GoF Reference:** Strategy (GoF, p. 315)

### Problem

The application has four user roles: farmer, buyer, transport, and admin. Each role has a different set of allowed pages, a different dashboard destination, and different data it may access. Without a pattern, this results in chains of `if ($role == 'farmer') { ... } elseif ($role == 'buyer') { ... }` scattered throughout every controller.

### Solution

`Auth::requireRole()` implements the Strategy pattern — the authentication *strategy* applied to a route is passed as a parameter. The method selects and executes the correct access control rule at runtime.

**Concrete role strategies:**

```php
// Farmer-only strategy
class FarmerController {
    public function __construct() {
        Auth::requireRole(['farmer']); // ← strategy: farmer only
    }
}

// Buyer-only strategy
class BuyerController {
    public function __construct() {
        Auth::requireRole(['buyer']); // ← strategy: buyer only
    }
}

// Multi-role strategy (admin and transport can view)
Auth::requireRole(['admin', 'transport']);

// Any authenticated user
Auth::require();
```

**Role-based redirect strategy in Auth:**

```php
public static function redirectToDashboard(): void {
    $url = match(Session::userRole()) {           // ← selects strategy at runtime
        'farmer'    => APP_URL . '/farmer/dashboard',
        'buyer'     => APP_URL . '/buyer/marketplace',
        'transport' => APP_URL . '/transport/dashboard',
        'admin'     => APP_URL . '/admin/dashboard',
        default     => APP_URL . '/login',
    };
    header('Location: ' . $url);
    exit;
}
```

Adding a new role requires adding one line to the `match` expression and one route — no existing code changes.

### Benefit

- Access control is defined at the point of use, not buried in if/else chains.
- New roles can be added without modifying existing role logic.
- Role strategies are interchangeable at runtime.

---

## 8. Observer Pattern

**Category:** Behavioral  
**GoF Reference:** Observer (GoF, p. 293)

### Problem

When an order is placed, multiple parties need to know: the farmer must be notified, and if stock drops below a threshold, another alert is needed. Putting all notification code inside `BuyerController::doPlaceOrder()` creates tight coupling between the ordering process and the notification system.

### Solution

`NotificationModel` acts as the notification channel (the "observer sink"). Business events trigger notifications by calling `$this->notif->create(...)`. The notification system is a separate concern — the order placement code does not know or care how notifications are stored or displayed.

**Event: Order Placed — BuyerController.php**

```php
public function doPlaceOrder(): void {
    // ... order creation ...
    $this->order->create([...]);

    // ── Notify farmer (Observer sink) ──────────────────────────────
    $this->notif->create(
        $listing['farmer_id'],
        'order_placed',
        'New Order Received',
        "$buyerName ordered {$quantity} {$listing['unit']} of {$listing['name']}.",
        '/farmer/orders'
    );

    // ── Low stock check → secondary notification ───────────────────
    $remaining = $listing['quantity'] - $quantity;
    if ($remaining <= ($listing['low_stock_threshold'] ?? 10)) {
        $this->notif->create(
            $listing['farmer_id'],
            'low_stock',
            'Low Stock Alert',
            "Your listing '{$listing['name']}' has only {$remaining} {$listing['unit']} remaining.",
            '/farmer/listings'
        );
    }
}
```

**Events that publish notifications across the application:**

| Event (Subject) | Observer Action | Recipient |
|---|---|---|
| Order placed | "New Order Received" | Farmer |
| Stock below threshold | "Low Stock Alert" | Farmer |
| Order confirmed | "Order Confirmed" | Buyer |
| Bid submitted | "New Bid Received" | Farmer |
| Bid accepted | "Bid Accepted" | Buyer |
| Delivery status updated | "Delivery Update" | Buyer |

**NotificationModel.php — the Observer sink:**

```php
class NotificationModel {
    public function create(int $userId, string $type, string $title, string $message, ?string $link = null): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO notifications (user_id, type, title, message, link) VALUES (?,?,?,?,?)"
        );
        return $stmt->execute([$userId, $type, $title, $message, $link]);
    }

    public function countUnread(int $userId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }
}
```

### Benefit

- The order placement logic does not couple to the notification display logic.
- New event types (e.g., "Farmer Verified") require only adding a `notif->create()` call at the event site.
- Notification rendering (the topbar badge, the notifications page) is completely independent.

---

## 9. Template Method / Composite View

**Category:** Behavioral / Structural  
**GoF Reference:** Template Method (GoF, p. 325)

### Problem

Every page in the application needs the same HTML `<head>`, sidebar, topbar, and footer. Without a shared structure, these would be copy-pasted into 20+ view files. Any change to the sidebar requires editing every file.

### Solution

AgriLink uses a **Composite View** pattern: shared UI fragments are extracted into partials, and every page view includes them using a consistent "template algorithm." The structure of a page is defined once; only the main content block differs per view.

**Partial structure:**

```
app/views/partials/
├── head.php       ← <html>, <head>, CSS includes
├── topbar.php     ← notification bell, user avatar, logout
├── sidebar.php    ← role-aware navigation menu
├── mobile_nav.php ← responsive bottom navigation
├── alerts.php     ← flash message display
└── foot.php       ← closing </body>, JS includes
```

**Every page view follows the same algorithm:**

```php
<?php include BASE_PATH . '/app/views/partials/head.php'; ?>
<?php include BASE_PATH . '/app/views/partials/topbar.php'; ?>
<?php include BASE_PATH . '/app/views/partials/sidebar.php'; ?>
<?php include BASE_PATH . '/app/views/partials/alerts.php'; ?>

<!-- PAGE-SPECIFIC CONTENT (the "step" that varies) -->
<main>
    ... farmer-specific or buyer-specific HTML ...
</main>

<?php include BASE_PATH . '/app/views/partials/foot.php'; ?>
```

The `sidebar.php` partial itself uses a conditional to render role-appropriate navigation — it is a single file serving all four roles:

```php
// sidebar.php
$role = Session::userRole();
if ($role === 'farmer') {
    // render farmer nav links
} elseif ($role === 'buyer') {
    // render buyer nav links
}
// ...
```

### Benefit

- The global navigation, topbar, and footer are defined in one place — one change affects all 20+ pages.
- New pages follow a defined template algorithm — developers cannot accidentally omit auth checks or the sidebar.
- The pattern enforces UI consistency across the entire application.

---

## 10. Specification / Filter Builder

**Category:** Behavioral  
**GoF Reference:** Related to Specification pattern (DDD) and Builder pattern (GoF, p. 97)

### Problem

The marketplace must support multiple simultaneous search criteria: category, region, price range, search keyword, verified farmers only, and sort order. Building SQL conditionally for every combination requires deeply nested if/else chains and is extremely difficult to maintain safely.

### Solution

`ProduceModel::getAll()` implements a **Filter Builder** — callers pass a filter specification array; the method builds the `WHERE` clause dynamically using parameterised queries, preventing SQL injection.

**File: `app/models/ProduceModel.php`**

```php
public function getAll(array $filters = []): array {
    $where  = ['p.status != "archived"'];  // ← base condition always applied
    $params = [];

    // ── Each filter is a specification ─────────────────────────────
    if (!empty($filters['status'])) {
        $where[] = 'p.status = ?';
        $params[] = $filters['status'];
    }
    if (!empty($filters['category'])) {
        $where[] = 'p.category = ?';
        $params[] = $filters['category'];
    }
    if (!empty($filters['region'])) {
        $where[] = 'p.region = ?';
        $params[] = $filters['region'];
    }
    if (!empty($filters['search'])) {
        $where[] = '(p.name LIKE ? OR p.description LIKE ?)';
        $params[] = '%' . $filters['search'] . '%';
        $params[] = '%' . $filters['search'] . '%';
    }
    if (!empty($filters['min_price'])) {
        $where[] = 'p.price_per_unit >= ?';
        $params[] = $filters['min_price'];
    }
    if (!empty($filters['max_price'])) {
        $where[] = 'p.price_per_unit <= ?';
        $params[] = $filters['max_price'];
    }
    if (!empty($filters['verified_only'])) {
        $where[] = 'u.is_verified = 1';
    }

    // ── ORDER BY whitelist (prevents SQL injection via ORDER BY) ───
    $allowedOrders = [
        'p.created_at DESC', 'p.price_per_unit ASC',
        'p.price_per_unit DESC', 'p.name ASC',
        'farmer_avg_rating DESC',
    ];
    $order = in_array($filters['order'] ?? '', $allowedOrders, true)
        ? $filters['order']
        : 'p.created_at DESC';  // safe default

    $whereSql = 'WHERE ' . implode(' AND ', $where);
    $stmt = $this->db->prepare("SELECT ... FROM produce p JOIN ... $whereSql ORDER BY $order");
    $stmt->execute($params);  // ← parameterised — no SQL injection possible
    return $stmt->fetchAll();
}
```

**Caller side — BuyerController.php:**

```php
// Composing a filter specification
$filters = [
    'region'        => 'Ashanti',
    'category'      => 'cereals',
    'min_price'     => 50,
    'max_price'     => 300,
    'verified_only' => true,
    'order'         => 'p.price_per_unit ASC',
];
$listings = $this->produce->getAll($filters);
```

### Benefit

- Any combination of up to 7 filter criteria is handled without combinatorial explosion.
- SQL injection is impossible: user input only appears in parameterised `?` placeholders.
- Adding a new filter (e.g., harvest month) is a single `if (!empty(...))` block in one method.

---

## 11. Chain of Responsibility

**Category:** Behavioral  
**GoF Reference:** Chain of Responsibility (GoF, p. 223)

### Problem

A request to `farmer/listings/add` must pass through multiple checks: is the user logged in? is their role farmer? is the CSRF token valid? Inline if/else chains for each check create deeply nested, brittle code.

### Solution

AgriLink applies a **Chain of Responsibility** through the auth and validation flow. Each guard in the chain either passes the request to the next guard or halts processing with a redirect or error.

```
HTTP Request
     │
     ▼
[1] Session::start()          ← is session valid / active?
     │ pass
     ▼
[2] Auth::requireRole(['farmer'])   ← is user logged in?
     │ fail → redirect to /login
     │ pass
     ▼
[3] Auth::requireRole check role    ← is role == 'farmer'?
     │ fail → 403 Forbidden
     │ pass
     ▼
[4] Session::verifyCsrf($_POST['_token'])  ← is token valid?
     │ fail → flash error + redirect
     │ pass
     ▼
[5] Input validation (required fields, types, ranges)
     │ fail → flash errors + redirect
     │ pass
     ▼
[6] Controller business logic executes
```

**Code from `FarmerController::doAddListing()`:**

```php
public function doAddListing(): void {
    Auth::requireRole(['farmer']);                           // [2,3] auth chain
    if (!Session::verifyCsrf($_POST['_token'] ?? '')) {    // [4] CSRF check
        Session::setFlash('error', 'Invalid request token.');
        Auth::redirect('/farmer/listings/add');
    }

    // [5] Input validation chain
    if (!$name)      $errors[] = 'Produce name is required.';
    if ($quantity <= 0) $errors[] = 'Quantity must be greater than zero.';
    if ($price <= 0)    $errors[] = 'Price must be greater than zero.';

    if ($errors) {
        Session::setFlash('error', implode('<br>', $errors));
        Auth::redirect('/farmer/listings/add');             // halt chain
    }

    // [6] All guards passed — execute
    $this->produce->create([...]);
}
```

### Benefit

- No request can reach business logic without passing all guards.
- Each check is self-contained and independently modifiable.
- Security is layered: a bypass of one layer is caught by the next.

---

## 12. Factory Method

**Category:** Creational  
**GoF Reference:** Factory Method (GoF, p. 107)

### Problem

Status values like `pending`, `in_transit`, `delivered`, and `cancelled` appear throughout the application. Rendering each with the correct colour, label, and styling requires repetitive conditional logic in every view.

### Solution

`Helpers::statusBadge()` is a **Factory Method** — given a status string, it creates and returns the appropriate HTML badge object (a formatted HTML string) without the caller needing to know the rendering details.

**File: `app/core/Helpers.php`**

```php
public static function statusBadge(string $status): string {
    $map = [
        'available'  => ['bg-green-100 text-green-800',   'Available'],
        'reserved'   => ['bg-yellow-100 text-yellow-800', 'Reserved'],
        'sold'       => ['bg-gray-100 text-gray-600',     'Sold'],
        'pending'    => ['bg-yellow-100 text-yellow-800', 'Pending'],
        'confirmed'  => ['bg-blue-100 text-blue-800',     'Confirmed'],
        'in_transit' => ['bg-cyan-100 text-cyan-800',     'In Transit'],
        'delivered'  => ['bg-green-100 text-green-800',   'Delivered'],
        'cancelled'  => ['bg-red-100 text-red-700',       'Cancelled'],
    ];
    [$classes, $label] = $map[$status] ?? ['bg-gray-100 text-gray-600', ucfirst($status)];

    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold '
         . $classes . '">' . self::e($label) . '</span>';
}
```

**Usage — all views (farmer orders, buyer orders, admin panel):**

```php
echo Helpers::statusBadge($order['status']);
echo Helpers::statusBadge($listing['status']);
echo Helpers::statusBadge($delivery['status']);
```

Adding a new status (`returned`, `disputed`) requires one line in `$map` — zero changes in view files.

### Benefit

- Badge appearance is defined once and applied consistently across all 20+ views.
- XSS protection (`self::e()`) is applied inside the factory — impossible to forget in a view.
- New statuses are added in one place with no view changes required.

---

## 13. Summary Table

| # | Pattern | Category | GoF | Location in AgriLink |
|---|---|---|---|---|
| 1 | Model-View-Controller | Architectural | Compound | Entire app structure |
| 2 | Front Controller | Architectural | Command | `public/index.php` |
| 3 | Layered Architecture | Architectural | — | `app/` directory structure |
| 4 | Repository | Data Access | Gateway | `app/models/**Model.php` |
| 5 | Singleton | Creational | Yes | `app/config/database.php` |
| 6 | Facade | Structural | Yes | `Auth`, `Session`, `Helpers` |
| 7 | Strategy | Behavioral | Yes | `Auth::requireRole()` |
| 8 | Observer | Behavioral | Yes | `NotificationModel::create()` |
| 9 | Template Method / Composite View | Behavioral | Yes | `app/views/partials/` |
| 10 | Specification / Filter Builder | Behavioral | Related to Builder | `ProduceModel::getAll()` |
| 11 | Chain of Responsibility | Behavioral | Yes | Auth + CSRF + validation flow |
| 12 | Factory Method | Creational | Yes | `Helpers::statusBadge()` |

---

## Engineering Observations

### Security is a first-class design concern

Design patterns were selected not just for maintainability but for security:
- **Singleton** ensures credentials are configured once and never re-exposed.
- **Chain of Responsibility** makes it structurally impossible to reach business logic without passing auth, CSRF, and input validation guards.
- **Facade (Session)** centralises CSRF token generation and verification, ensuring it is never inconsistently applied.
- **Filter Builder** uses parameterised queries exclusively — SQL injection via filter inputs is architecturally impossible.

### Patterns working together

Many patterns in AgriLink are composites — they work because they are applied together:
- The **Front Controller** bootstraps the **Facade** classes (Auth, Session, Helpers).
- The **Repository** pattern depends on the **Singleton** for its database connection.
- The **Strategy** pattern is the enforcement mechanism applied in the **Chain of Responsibility**.
- The **Observer** notification system is triggered by business events coordinated by the **MVC** controllers.

### Relationship to SOLID principles

| SOLID Principle | Pattern Enforcing It |
|---|---|
| Single Responsibility | MVC, Repository, Layered Architecture |
| Open/Closed | Strategy, Factory Method, Filter Builder |
| Liskov Substitution | Strategy (interchangeable roles) |
| Interface Segregation | Repository (domain-specific model interfaces) |
| Dependency Inversion | Singleton injection into Repository constructors |

---

*Document prepared for CS 415 Software Engineering Final Project — AgriLink Ghana Agricultural Marketplace Platform*  
*Team: [Team Name] | Date: April 2026*
