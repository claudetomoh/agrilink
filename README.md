# AgriLink Ghana — Agricultural Marketplace Platform

> A full-stack PHP MVC web application connecting Ghanaian farmers, buyers, and transport providers in a unified digital marketplace.

**Live Demo:** [http://169.239.251.102:280/~tomoh.ikfingeh/agrilink/public/](http://169.239.251.102:280/~tomoh.ikfingeh/agrilink/public/)  
**GitHub:** [https://github.com/claudetomoh/agrilink](https://github.com/claudetomoh/agrilink)

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [Installation & Local Setup](#installation--local-setup)
- [Deployment](#deployment)
- [Demo Accounts](#demo-accounts)
- [Routes Reference](#routes-reference)
- [Screenshots](#screenshots)

---

## Overview

AgriLink Ghana is an agricultural marketplace platform purpose-built for the Ghanaian farming ecosystem. Farmers can list produce, buyers can browse and place orders with price bidding, and transport providers manage delivery logistics — all through a single integrated platform. Admins oversee the entire ecosystem with analytics dashboards and user verification controls.

---

## Features

### Farmer Portal
- **Produce Listings** — Create, edit, and remove listings with quantity, price, location, and harvest date
- **Order Management** — View and accept/reject incoming orders from buyers
- **Bid Negotiation** — Accept or counter buyer bids with real-time notifications
- **Farmer Profile** — Add farm name, region, and Ghana Card verification documents
- **Dashboard** — Summary cards for active listings, pending orders, and revenue

### Buyer Portal
- **Marketplace** — Browse all available produce with search, category, location, and price filters
- **Verified Farmers Filter** — Filter results to show only produce from verified farmers
- **Demand Matching** — AI-assisted supply/demand matching based on category and location
- **Price Bidding** — Submit bids below asking price; receive farmer counter-offers
- **Order Tracking** — Real-time status updates from placement through delivery
- **Review System** — Leave ratings and reviews on completed orders

### Transport Portal
- **Job Board** — View available delivery jobs matched by location
- **Delivery Timeline** — Step-by-step job status updates (picked up → in transit → delivered)
- **Transport Dashboard** — Active jobs and earnings overview

### Admin Panel
- **User Management** — View all users, verify farmers, deactivate accounts
- **Order Oversight** — Monitor all orders across the platform
- **Delivery Monitoring** — Track all active deliveries
- **Analytics Dashboard** — Platform-wide metrics and market analytics

### Platform-Wide
- **Real-time Notifications** — In-app notifications for order status, bids, and reviews
- **Ghana Region Support** — All 16 regions of Ghana available for location filtering
- **Role-based Access Control** — Strict middleware-enforced permissions per role
- **Responsive UI** — Mobile-first layouts using Tailwind CSS

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Language | PHP 8.0+ |
| Architecture | Custom MVC (no external framework) |
| Database | MySQL 8.0 |
| Frontend | Tailwind CSS (CDN), Vanilla JS |
| Icons | Material Symbols (Google Fonts) |
| Typography | Manrope / Inter (Google Fonts) |
| Web Server | Apache (with mod_rewrite) |

---

## Project Structure

```
agrilink/
├── app/
│   ├── config/
│   │   ├── config.php          # DB credentials, APP_URL, constants (gitignored)
│   │   └── database.php        # PDO connection singleton
│   ├── controllers/
│   │   ├── HomeController.php
│   │   ├── AuthController.php
│   │   ├── FarmerController.php
│   │   ├── BuyerController.php
│   │   ├── TransportController.php
│   │   ├── AdminController.php
│   │   ├── AnalyticsController.php
│   │   └── NotificationController.php
│   ├── core/
│   │   ├── Auth.php            # Role-based access middleware
│   │   ├── Session.php         # Session management helpers
│   │   └── Helpers.php         # Utility functions
│   ├── models/
│   │   ├── UserModel.php
│   │   ├── ProduceModel.php
│   │   ├── OrderModel.php
│   │   ├── BidModel.php
│   │   ├── DeliveryModel.php
│   │   ├── ReviewModel.php
│   │   └── NotificationModel.php
│   └── views/
│       ├── admin/              # Admin dashboard, users, orders, deliveries
│       ├── analytics/          # Market analytics dashboard
│       ├── auth/               # Login, register, onboarding
│       ├── buyer/              # Marketplace, orders, matching, reviews
│       ├── errors/             # 404 page
│       ├── farmer/             # Dashboard, listings, orders, profile
│       ├── home/               # Landing page
│       ├── notifications/      # Notification feed
│       ├── partials/           # Shared: topbar, sidebar, head, foot, alerts
│       └── transport/          # Job board, delivery timeline
├── database/
│   ├── schema.sql              # Table definitions
│   ├── migration.sql           # Indexes and foreign key constraints
│   └── seed.sql                # Demo users, produce, orders, bids
├── public/
│   ├── index.php               # Front controller (all requests route here)
│   ├── .htaccess               # mod_rewrite rules
│   ├── css/
│   │   ├── app.css             # Custom overrides
│   │   └── icons.css           # Icon helper classes
│   ├── js/
│   │   └── app.js              # Client-side interactivity
│   └── uploads/
│       └── produce/            # Uploaded produce images (gitignored)
├── routes/                     # Route definitions (loaded by front controller)
├── .gitignore
└── README.md
```

---

## Database Schema

### Core Tables

| Table | Description |
|-------|-------------|
| `users` | All user accounts with role (`farmer`, `buyer`, `transport`, `admin`), region, and verification status |
| `produce_listings` | Farmer produce with quantity, unit, price, category, location, harvest date, status |
| `orders` | Buyer orders linking to produce listings; tracks status through the lifecycle |
| `bids` | Price negotiation records between buyers and farmers |
| `deliveries` | Transport job records with status timeline |
| `reviews` | Buyer ratings on completed orders |
| `notifications` | In-app notification records per user |

### Order Lifecycle

```
pending → accepted → in_delivery → delivered → completed
                  ↘ rejected
```

### Bid Lifecycle

```
pending → accepted
        ↘ rejected
        ↘ countered → (buyer re-bids)
```

---

## Installation & Local Setup

### Prerequisites
- PHP 8.0+
- MySQL 8.0+
- Apache with `mod_rewrite` enabled (or Nginx with equivalent config)
- Composer (optional — no Composer dependencies currently)

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/claudetomoh/agrilink.git
   cd agrilink
   ```

2. **Create the database**
   ```sql
   CREATE DATABASE agrilink_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Import database files** (in order)
   ```bash
   mysql -u root -p agrilink_db < database/schema.sql
   mysql -u root -p agrilink_db < database/migration.sql
   mysql -u root -p agrilink_db < database/seed.sql
   ```

4. **Create configuration file**

   Create `app/config/config.php` (this file is gitignored to keep credentials safe):
   ```php
   <?php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'agrilink_db');
   define('DB_USER', 'your_mysql_user');
   define('DB_PASS', 'your_mysql_password');
   define('APP_URL', 'http://localhost/agrilink/public');
   define('APP_NAME', 'AgriLink Ghana');
   ```

5. **Configure Apache VirtualHost or use `.htaccess`**

   Point your document root at the `public/` directory, or configure a VirtualHost:
   ```apache
   <VirtualHost *:80>
       DocumentRoot /path/to/agrilink/public
       DirectoryIndex index.php
       <Directory /path/to/agrilink/public>
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

   If running in a subdirectory (e.g., `localhost/agrilink/public/`), update `public/.htaccess`:
   ```apache
   RewriteBase /agrilink/public/
   ```

6. **Set upload directory permissions**
   ```bash
   chmod 775 public/uploads/produce
   ```

7. **Visit the app**
   ```
   http://localhost/agrilink/public/
   ```

---

## Deployment

The app is deployed to a shared Apache server with PHP 8.3 and MySQL 8.0.

### Server Details
- **Host:** `169.239.251.102` (port 280 for HTTP, port 222 for SSH)
- **Web root:** `/home/tomoh.ikfingeh/public_html/agrilink/`
- **App URL:** `http://169.239.251.102:280/~tomoh.ikfingeh/agrilink/public/`

### Key Deployment Differences from Local
- `RewriteBase` in `public/.htaccess` is set to `/~tomoh.ikfingeh/agrilink/public/`
- `APP_URL` in `config.php` points to the full server URL
- Database name follows the server's naming convention (`mobileapps_2026B_<username>`)
- `config.php` is excluded from version control — must be manually uploaded or created on server

### Deploy Updated Files
```bash
# SSH into server
ssh -p 222 tomoh.ikfingeh@169.239.251.102

# Navigate to app directory
cd ~/public_html/agrilink

# Pull latest changes (if git is configured on server)
git pull origin main
```

---

## Demo Accounts

All demo accounts use the password: **`Pass@1234`**

| Role | Email | Notes |
|------|-------|-------|
| Admin | `admin@agrilink.gh` | Full platform access |
| Farmer (Verified) | `kofi.boateng@agrilink.gh` | Verified badge, active listings |
| Farmer (Unverified) | `ama.owusu@agrilink.gh` | Unverified status |
| Buyer | `efua.asante@agrilink.gh` | Active orders and bids |
| Transport | `kweku.mensah@agrilink.gh` | Active delivery jobs |

---

## Routes Reference

All routes are handled by `public/index.php` via URL query parameter `?url=`.

### Public Routes
| Route | Description |
|-------|-------------|
| `/` | Landing page |
| `/login` | Login form |
| `/register` | Registration form |
| `/onboarding` | Role selection |

### Farmer Routes (requires `farmer` role)
| Route | Description |
|-------|-------------|
| `/farmer/dashboard` | Farmer home |
| `/farmer/listings` | My produce listings |
| `/farmer/add-listing` | New listing form |
| `/farmer/edit-listing?id=` | Edit existing listing |
| `/farmer/orders` | Incoming orders |
| `/farmer/profile` | Edit profile |

### Buyer Routes (requires `buyer` role)
| Route | Description |
|-------|-------------|
| `/buyer/marketplace` | Browse all produce |
| `/buyer/product?id=` | Produce detail + bid |
| `/buyer/orders` | My orders |
| `/buyer/matching` | Demand matching |
| `/buyer/review?order_id=` | Leave a review |

### Transport Routes (requires `transport` role)
| Route | Description |
|-------|-------------|
| `/transport/dashboard` | Transport home |
| `/transport/jobs` | Available jobs |
| `/transport/delivery?id=` | Delivery timeline |

### Admin Routes (requires `admin` role)
| Route | Description |
|-------|-------------|
| `/admin/dashboard` | Admin overview |
| `/admin/users` | User management |
| `/admin/orders` | All orders |
| `/admin/deliveries` | All deliveries |

### Analytics & Notifications
| Route | Description |
|-------|-------------|
| `/analytics` | Market analytics dashboard |
| `/notifications` | In-app notifications |

---

## Screenshots

> Screenshots coming soon. Visit the [live demo](http://169.239.251.102:280/~tomoh.ikfingeh/agrilink/public/) to see the platform in action.

---

## Contributing

This project was developed as part of a university web development course. Pull requests are welcome for bug fixes and improvements.

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature`
3. Commit your changes: `git commit -m 'Add your feature'`
4. Push to the branch: `git push origin feature/your-feature`
5. Open a Pull Request

---

## License

This project is for educational purposes. All rights reserved © 2025 Tomoh Ikfingeh.
