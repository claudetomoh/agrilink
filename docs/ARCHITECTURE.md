# AgriLink Ghana — System Architecture Document

**CS 415: Software Engineering | Final Project Documentation**  
**Module: System Design & Architecture**

---

## 1. Overview

AgriLink Ghana is a custom-built PHP MVC web application. The system connects three primary actors — Farmers, Buyers, and Transport Providers — through a unified digital agricultural marketplace, with an Admin layer overseeing the entire platform.

**Live System:** http://169.239.251.102:280/~tomoh.ikfingeh/agrilink/public/  
**Repository:** https://github.com/claudetomoh/agrilink

---

## 2. High-Level Architecture

```
┌─────────────────────────────────────────────────────────────────────────┐
│                           CLIENT LAYER                                  │
│          Browser (Farmer | Buyer | Transport | Admin)                   │
│          HTML5 / CSS3 (Tailwind) / Vanilla JavaScript                   │
└─────────────────────────────┬───────────────────────────────────────────┘
                              │ HTTP/HTTPS
┌─────────────────────────────▼───────────────────────────────────────────┐
│                       WEB SERVER LAYER                                  │
│               Apache 2.4 + mod_rewrite (.htaccess)                      │
│        All requests rewritten → public/index.php (Front Controller)     │
└─────────────────────────────┬───────────────────────────────────────────┘
                              │
┌─────────────────────────────▼───────────────────────────────────────────┐
│                    APPLICATION LAYER (PHP 8.0+)                         │
│                                                                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  ┌────────────┐ │
│  │   Auth.php   │  │  Session.php │  │  Helpers.php │  │ Mailer.php │ │
│  │  (Strategy / │  │  (Facade /   │  │  (Facade /   │  │            │ │
│  │   Facade)    │  │  Singleton)  │  │  Factory)    │  │            │ │
│  └──────────────┘  └──────────────┘  └──────────────┘  └────────────┘ │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │                      CONTROLLERS                                  │  │
│  │  HomeController  FarmerController  BuyerController               │  │
│  │  AuthController  TransportController  AdminController            │  │
│  │  AnalyticsController  NotificationController                     │  │
│  └──────────────────────────────────────────────────────────────────┘  │
└─────────────────────────────┬───────────────────────────────────────────┘
                              │
┌─────────────────────────────▼───────────────────────────────────────────┐
│                       DATA ACCESS LAYER                                 │
│                                                                         │
│  ┌───────────┐ ┌──────────────┐ ┌──────────┐ ┌──────────────────────┐ │
│  │ UserModel │ │ ProduceModel │ │OrderModel│ │NotificationModel     │ │
│  └───────────┘ └──────────────┘ └──────────┘ └──────────────────────┘ │
│  ┌───────────┐ ┌──────────────┐ ┌──────────┐                          │
│  │ BidModel  │ │DeliveryModel │ │ReviewModel│                         │
│  └───────────┘ └──────────────┘ └──────────┘                          │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │              Database (Singleton PDO Connection)                  │  │
│  └──────────────────────────────────────────────────────────────────┘  │
└─────────────────────────────┬───────────────────────────────────────────┘
                              │ PDO / MySQL
┌─────────────────────────────▼───────────────────────────────────────────┐
│                       PERSISTENCE LAYER                                 │
│                  MySQL 8.0 — mobileapps_2026B database                  │
│  Tables: users, produce, orders, bids, deliveries, reviews,             │
│          notifications                                                   │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Component Diagram

```
                    ┌─────────────────────────────────────┐
                    │           public/index.php           │
                    │         (Front Controller)           │
                    │  • Bootstraps config                 │
                    │  • Autoloads models + controllers    │
                    │  • Starts session                    │
                    │  • Resolves route → dispatches       │
                    └──────────────────┬──────────────────┘
                                       │ instantiates & calls
          ┌────────────────────────────┼────────────────────────────┐
          │                            │                            │
          ▼                            ▼                            ▼
┌─────────────────┐         ┌─────────────────┐         ┌─────────────────┐
│ FarmerController│         │ BuyerController │         │ AdminController │
│                 │         │                 │         │                 │
│ • dashboard()   │         │ • marketplace() │         │ • dashboard()   │
│ • listings()    │         │ • productDetail │         │ • users()       │
│ • addListing()  │         │ • doPlaceOrder()│         │ • orders()      │
│ • orders()      │         │ • orders()      │         │ • deliveries()  │
│ • profile()     │         │ • matching()    │         │                 │
└────────┬────────┘         └────────┬────────┘         └────────┬────────┘
         │ uses                      │ uses                      │ uses
         ▼                           ▼                           ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                            MODEL LAYER                                  │
│  ProduceModel   OrderModel   UserModel   BidModel   NotificationModel   │
│  DeliveryModel  ReviewModel                                             │
└─────────────────────────────┬───────────────────────────────────────────┘
                              │ all use
                              ▼
                    ┌─────────────────┐
                    │    Database     │
                    │  (Singleton)    │
                    │ PDO::connect()  │
                    └─────────────────┘
```

---

## 4. Request Lifecycle

A complete walkthrough of a farmer submitting a new produce listing:

```
1. Browser: POST /farmer/listings/add
   → Apache .htaccess rewrites → index.php?url=farmer/listings/add

2. index.php:
   • Loads config.php, database.php
   • Autoloads all models (*.php in /models/)
   • Autoloads all controllers (*.php in /controllers/)
   • Starts Session
   • Parses URL → segments ['farmer', 'listings', 'add']
   • Looks up route → ['FarmerController', 'doAddListing']
   • Instantiates FarmerController (Auth::requireRole(['farmer']) called in __construct)
   • Calls doAddListing()

3. FarmerController::doAddListing():
   • Auth::requireRole(['farmer'])  → passes (user is farmer)
   • Session::verifyCsrf($token)    → passes (token valid)
   • Sanitise & validate inputs     → passes
   • $this->produce->create([...])  → ProduceModel::create()
   • $this->notif->create(...)      → NotificationModel::create()
   • Session::flash('success', ...) → sets flash message
   • Auth::redirect('/farmer/listings')

4. Browser: GET /farmer/listings
   → FarmerController::listings()
   → ProduceModel::getByFarmer($farmerId)
   → include 'app/views/farmer/listings.php'

5. listings.php:
   • include partials/head.php
   • include partials/topbar.php  (shows notification count)
   • include partials/sidebar.php
   • include partials/alerts.php  (displays success flash)
   • Renders listing cards with produce data
   • include partials/foot.php
```

---

## 5. Database Schema — Entity Relationship

```
users (id, name, email, role, region, is_verified, is_active)
  │
  ├──< produce (farmer_id → users.id)
  │     id, name, category, quantity, unit, price_per_unit,
  │     region, status, harvest_date
  │
  ├──< orders (buyer_id → users.id, farmer_id → users.id)
  │     id, order_ref, produce_id, quantity, unit_price,
  │     total_price, status, delivery_address
  │       │
  │       └──< deliveries (order_id → orders.id)
  │               id, transport_id → users.id,
  │               status, origin, destination,
  │               picked_up_at, delivered_at
  │
  ├──< bids (buyer_id → users.id, produce_id → produce.id)
  │     id, amount, status (pending/accepted/rejected/countered)
  │
  ├──< reviews (reviewer_id → users.id, reviewee_id → users.id)
  │     id, order_id, rating (1-5), comment
  │
  └──< notifications (user_id → users.id)
        id, type, title, message, link, is_read
```

### Order Lifecycle (State Machine)

```
pending ──→ confirmed ──→ processing ──→ in_transit ──→ delivered
    └──────────────────────────────────────────────────→ cancelled
```

### Bid Lifecycle (State Machine)

```
pending ──→ accepted
        └──→ rejected
        └──→ countered ──→ (new buyer bid)
```

---

## 6. Security Architecture

AgriLink applies defence-in-depth: multiple independent security layers mean a failure in one layer does not expose the system.

| Threat | Countermeasure | Location |
|---|---|---|
| SQL Injection | Parameterised PDO queries exclusively; ORDER BY whitelist | All models |
| XSS (Cross-Site Scripting) | `Helpers::e()` wraps all output in `htmlspecialchars()` | All views |
| CSRF (Cross-Site Request Forgery) | `Session::csrfToken()` + `verifyCsrf()` on all POST forms | Session.php, all controllers |
| Unauthorised Access | `Auth::requireRole()` in every controller constructor | Auth.php |
| Session Fixation | `session_regenerate_id(true)` on login + every 30 min | Session.php |
| Session Hijacking | `httponly=true`, `samesite=Strict`, `secure` cookie flags | Session.php |
| Credential Exposure | Passwords hashed with `password_hash(BCRYPT, cost=12)` | UserModel.php |
| Password Reset Abuse | Tokens hashed, expiry timestamp, single-use, cleared on use | UserModel.php |
| Directory Traversal | Webroot is `public/` only; `app/` is above webroot | Apache config |
| Error Information Leakage | DB errors logged to file, never exposed to browser | database.php |

---

## 7. Role-Based Access Control

Four user roles with strict separation of access:

| Role | Entry Point | Permissions |
|---|---|---|
| **Farmer** | `/farmer/dashboard` | Manage own listings, view/accept own orders, view bids |
| **Buyer** | `/buyer/marketplace` | Browse listings, place orders, submit bids, write reviews |
| **Transport** | `/transport/dashboard` | View delivery jobs, update delivery status |
| **Admin** | `/admin/dashboard` | View all users/orders/deliveries, verify farmers, deactivate accounts |

Access is enforced at the controller constructor level. No route is accessible without the correct role:

```php
// FarmerController — ALL farmer routes protected
public function __construct() {
    Auth::requireRole(['farmer']); // called before ANY method
}
```

---

## 8. Technology Stack

| Component | Technology | Justification |
|---|---|---|
| Language | PHP 8.0+ | Mature, widely-deployed server-side language |
| Architecture | Custom MVC (no framework) | Demonstrates engineering understanding; no magic |
| Database | MySQL 8.0 | Relational data with ACID transactions |
| Frontend CSS | Tailwind CSS (CDN) | Utility-first, consistent design tokens |
| Frontend JS | Vanilla JavaScript | No build toolchain needed; transparent |
| Icons | Material Symbols (Google Fonts) | Comprehensive, consistent icon system |
| Typography | Manrope / Inter (Google Fonts) | Clean, professional, legible |
| Web Server | Apache + mod_rewrite | Standard, well-understood |
| Version Control | Git / GitHub | Industry standard |
| Image CDN | Unsplash + Pexels | High-quality, licence-free images |

### Why custom MVC over a framework?

Using Laravel, Symfony, or CodeIgniter would have abstracted away the very concepts this course teaches. A custom MVC implementation forces explicit understanding of routing, the request lifecycle, session management, input validation, and security. Every part of the system is intentionally written and understood — not generated or hidden by a framework.

---

## 9. Non-Functional Requirements and How They Are Met

| NFR | Requirement | Implementation |
|---|---|---|
| **Security** | No SQL injection, XSS, CSRF | Parameterised queries, `Helpers::e()`, CSRF tokens |
| **Performance** | Single DB connection per request | Database Singleton pattern |
| **Maintainability** | Changes in one place affect all | MVC, Repository, Composite View |
| **Reliability** | Data integrity across tables | Foreign key constraints, status ENUMs |
| **Usability** | Works on mobile and desktop | Tailwind responsive classes, mobile nav partial |
| **Accessibility** | Readable text on all backgrounds | Colour contrast audit; `bg-primary` (#2c694e) with white text |
| **Scalability** | New roles/features without rewrites | Strategy pattern, Front Controller route table |
| **Traceability** | Orders tracked end-to-end | Order lifecycle state machine, notification events |
| **Localisation** | Ghana-specific | All 16 regions, Ghana Cedi (₵), local produce categories |

---

## 10. Deployment Architecture

```
Developer Machine (Windows)
        │
        │  Python paramiko / SFTP
        │  deploy_setup.py
        ▼
Remote Server: 169.239.251.102:222 (SSH port)
        │
        ├── /home/tomoh.ikfingeh/public_html/agrilink/
        │     └── (application files)
        │
        └── Web Server (Apache :280)
              URL: http://169.239.251.102:280/~tomoh.ikfingeh/agrilink/public/
```

Database server: `169.239.251.102` MySQL (shared university server)  
Database name: `mobileapps_2026B_tomoh_ikfingeh`

---

*Document prepared for CS 415 Software Engineering Final Project — AgriLink Ghana Agricultural Marketplace Platform*  
*Team: [Team Name] | Date: April 2026*
