# AgriLink Ghana — Final Project Report

**CS 415: Software Engineering | Week 14 Submission**  
**Team:** AgriLink Development Team  
**Date:** April 2026  
**Live Application:** http://169.239.251.102:280/~tomoh.ikfingeh/agrilink/public/  
**Repository:** https://github.com/claudetomoh/agrilink  

---

## 1. Executive Summary

AgriLink Ghana is a multi-role agricultural marketplace web application built to digitise Ghana's agricultural value chain. The platform connects farmers, buyers, and transport providers in a single integrated ecosystem — enabling direct produce trading, price negotiation, delivery logistics, and real-time market analytics.

The project was executed over three two-week Agile sprints, delivering 34 user stories across 7 epics (83 total story points). The final platform is live, fully tested, and documented to professional engineering standards — demonstrating MVC architecture, 12 GoF design patterns, OWASP-compliant security, and a 43-test unit suite with 100% pass rate.

---

## 2. Project Overview

### 2.1 Problem Statement

Ghanaian smallholder farmers lose 30–40% of potential income to intermediaries and suffer 20–30% post-harvest losses due to market fragmentation and logistics opacity. Buyers lack real-time supply visibility; transport providers operate without a unified job board.

### 2.2 Solution

A full-stack PHP MVC platform with four distinct role portals:

| Role | Core Capability |
|---|---|
| **Farmer** | List produce, manage orders, accept/reject bids, track revenue |
| **Buyer** | Browse marketplace, place orders, submit bids, track deliveries |
| **Transport** | View job board, accept deliveries, update transit status |
| **Admin** | Verify farmers, monitor all orders/deliveries, view KPI dashboard |

### 2.3 Key Innovation

The **Supply-Demand Matching Engine** scores every listing against a buyer's profile across four dimensions:

| Dimension | Score |
|---|---|
| Same region as buyer | +40 pts |
| Category matches preference | +30 pts |
| Quantity ≥ buyer's minimum | +20 pts |
| Price within buyer's budget | +10 pts |
| **Maximum** | **100 pts** |

This reduces search friction by surfacing the highest-relevance produce first — a lightweight recommendation system without requiring ML infrastructure.

---

## 3. Team Structure & Scrum Roles

| Scrum Role | Member | Sprint Responsibilities |
|---|---|---|
| **Product Owner** | Tomoh Ikfingeh | Product backlog, user story prioritisation, sprint acceptance |
| **Scrum Master** | Mariana Eib | Sprint ceremonies, blocker removal, velocity tracking |
| **Lead Developer** | Tomoh Ikfingeh | MVC architecture, security layer, matching engine, test suite |
| **Frontend Developer** | Tomoh Ikfingeh | View templates, CSS design system, responsive layouts |
| **Backend Developer** | Albert Soaliye | Database schema, model layer, RBAC routing |
| **QA / Documentation** | Kingsford Amissah | Test plan, sprint docs, defect log |

**Project Management Tool:** GitHub Projects (Kanban board at github.com/claudetomoh/agrilink/projects) — tracks user stories from Backlog → In Progress → Done across sprints.

---

## 4. Agile Methodology & Sprint Execution

### 4.1 Scrum Ceremonies

All four Scrum ceremonies were conducted each sprint:

| Ceremony | Frequency | Output |
|---|---|---|
| Sprint Planning | Start of each sprint | Sprint backlog with story points |
| Daily Standup | Daily (async — written update in GitHub Issues) | Blockers surface and resolved |
| Sprint Review | End of sprint | Demo of working software; stories accepted/rejected |
| Retrospective | End of sprint | "What went well / improve / action items" — recorded in SPRINT.md |

### 4.2 Sprint 1 — Foundation & Authentication (Weeks 1–2)

**Goal:** MVC skeleton, database schema, secure authentication with RBAC.  
**Velocity:** 22/24 story points

**Sprint Review outcome:** All authentication stories accepted. Route extraction to `routes/` deferred to Sprint 2 backlog.

**Daily Standups (representative entries):**

| Date | Yesterday | Today | Blockers |
|---|---|---|---|
| Week 1, Day 2 | Set up project directory + PHP autoloader | Implement PDO Singleton in `database.php` | None |
| Week 1, Day 4 | Database Singleton complete | Build registration form + `UserModel::create()` | Deciding bcrypt cost factor (settled on 12) |
| Week 2, Day 1 | Registration + login working | Implement session fixation prevention (ID regen on login) | None |
| Week 2, Day 3 | RBAC middleware done | CSRF token system + form integration | Forms need `Session::csrfToken()` on every POST |
| Week 2, Day 5 | Sprint 1 complete | Sprint review + retrospective | None |

### 4.3 Sprint 2 — Core Portals (Weeks 3–4)

**Goal:** All four role portals, CRUD operations, order management, bidding, notifications.  
**Velocity:** 27/26 story points *(exceeded target)*

**Sprint Review outcome:** All 11 planned stories accepted. Two colour-contrast defects identified during review; logged as BUG-003 and backlogged for Sprint 3.

**Daily Standups (representative entries):**

| Date | Yesterday | Today | Blockers |
|---|---|---|---|
| Week 3, Day 1 | Sprint planning complete | Build `ProduceModel` with Specification/Filter pattern | None |
| Week 3, Day 3 | Farmer create/edit listing done | Build buyer marketplace with filter sidebar | Filter SQL needs whitelist for ORDER BY |
| Week 4, Day 1 | Buyer marketplace + product detail done | Order placement + `OrderModel` | Quantity validation on server side needed |
| Week 4, Day 3 | Order placement done | Transport job board + delivery status | Delivery status update needed in `TransportController` |
| Week 4, Day 5 | All portals complete | Sprint review — demo all portals | None |

### 4.4 Sprint 3 — Quality, Polish & Engineering Docs (Weeks 5–6)

**Goal:** Fix all known defects, add recommendations engine, formal documentation, unit tests.  
**Velocity:** 34/25 story points *(significantly exceeded — engineering sprint)*

**Sprint Review outcome:** All 12 stories accepted. 43/43 unit tests passing. 4 defects resolved. Full documentation suite delivered.

**Daily Standups (representative entries):**

| Date | Yesterday | Today | Blockers |
|---|---|---|---|
| Week 5, Day 1 | Sprint planning | Fix `produceImage()` substring bug (BUG-001) | Map ordering strategy needed (compound before simple) |
| Week 5, Day 3 | BUG-001 + BUG-002 fixed | Implement "Recommended for You" engine | None |
| Week 5, Day 5 | Recommendations engine working | Write unit tests for all `Helpers` methods | `money()` tests failed due to CURRENCY_SYMBOL mismatch — fixed |
| Week 6, Day 2 | 43/43 tests passing | Write ARCHITECTURE.md, ER diagram | None |
| Week 6, Day 4 | ARCHITECTURE + DESIGN_PATTERNS docs done | Write TEST_PLAN.md + SPRINT.md | None |
| Week 6, Day 5 | All docs complete | Final deploy, git push, sprint review | None |

### 4.5 Velocity Chart

| Sprint | Target | Actual | Δ |
|---|---|---|---|
| Sprint 1 | 24 pts | 22 pts | −2 |
| Sprint 2 | 26 pts | 27 pts | +1 |
| Sprint 3 | 25 pts | 34 pts | +9 |
| **Total** | **75 pts** | **83 pts** | **+8** |

---

## 5. System Architecture

AgriLink uses a **custom MVC architecture** with a **Front Controller** pattern — all requests enter through `public/index.php`, which resolves routes and dispatches to the appropriate controller.

```
Browser → public/index.php (Front Controller)
              ↓  route table
         XxxController.php (Controller)
              ↓  query          ↓ pass data
         XxxModel.php     views/xxx/yyy.php
              ↓
           MySQL (PDO Singleton)
```

**Layer separation enforced:**
- No SQL in views or controllers (Repository pattern in models)
- No HTML in models
- All output escaped via `Helpers::e()` (XSS prevention)
- All POST forms carry CSRF tokens

### Database Schema (entity summary)

| Table | Key Columns | Relationships |
|---|---|---|
| `agrilink_users` | id, name, email, password_hash, role, region, is_verified | Parent to produce, orders, deliveries, notifications |
| `agrilink_produce` | id, farmer_id, name, category, quantity, price_per_unit, unit, region, status | farmer_id → users |
| `agrilink_orders` | id, buyer_id, produce_id, quantity, total_price, status | buyer_id, produce_id → users, produce |
| `agrilink_deliveries` | id, order_id, transporter_id, origin, destination, status | order_id, transporter_id → orders, users |
| `agrilink_bids` | id, buyer_id, produce_id, bid_amount, status | buyer_id, produce_id → users, produce |
| `agrilink_reviews` | id, order_id, reviewer_id, rating, comment | order_id → orders |
| `agrilink_notifications` | id, user_id, message, type, is_read | user_id → users |

---

## 6. Key Features & Innovation

### 6.1 Supply-Demand Matching Engine
A scoring algorithm (`Helpers::matchScore()`) ranks every active listing against a buyer's profile in real time. Max 100 points across four criteria. Unit-tested with 5 test cases covering each dimension independently.

### 6.2 Role-Based Access Control (RBAC)
Every route is protected by `Auth::requireRole()`. A farmer attempting to access `/buyer/marketplace` gets a 403. Sessions are regenerated on login to prevent session fixation (OWASP A07).

### 6.3 Price Bidding
Buyers can submit bids below asking price. Farmers can accept, reject, or counter. Each bid action triggers an in-app notification via the Observer pattern.

### 6.4 Live Fleet Tracker
The transport dashboard embeds an OpenStreetMap iframe with Leaflet.js overlays showing active delivery routes and status — giving transport providers real-time route visibility.

### 6.5 Verified Farmer Badge
Admins can verify farmer accounts. Verified farmers get a ✓ badge on their listings. Buyers can filter the marketplace to show only verified-farmer produce — increasing trust in the platform.

### 6.6 Design Patterns (12 Implemented)

| Pattern | GoF Category | Implementation |
|---|---|---|
| MVC | Architectural | Entire codebase |
| Front Controller | Architectural | `public/index.php` |
| Layered Architecture | Architectural | `core/` → `models/` → `views/` |
| Repository | Data Access | All `*Model.php` |
| Singleton | Creational | `app/config/database.php` |
| Factory Method | Creational | `Helpers::statusBadge()` |
| Facade | Structural | `Auth.php`, `Session.php`, `Helpers.php` |
| Strategy | Behavioural | `Auth::requireRole()` per-role redirect |
| Observer | Behavioural | `NotificationModel::create()` |
| Template Method / Composite View | Structural | `partials/` (head, sidebar, topbar) |
| Specification / Filter Builder | Behavioural | `ProduceModel::getAll(array $filters)` |
| Chain of Responsibility | Behavioural | Controller middleware stack |

---

## 7. Security Implementation (OWASP Top 10)

| Threat | Mitigation |
|---|---|
| A01 – Broken Access Control | `Auth::requireRole()` on every route; 403 on violation |
| A02 – Cryptographic Failures | Bcrypt (cost 12) for passwords; HTTPS recommended for production |
| A03 – Injection (SQL) | PDO with parameterised queries throughout all models |
| A07 – Identification / Auth | Session ID regenerated on login; session timeout enforced |
| A08 – CSRF | `Session::csrfToken()` generated and validated on every POST |
| A03 – XSS | All output passed through `Helpers::e()` (htmlspecialchars ENT_QUOTES) |

---

## 8. Challenges Faced

### 8.1 Substring False-Match in `produceImage()`
**Challenge:** `str_contains('Kontomire', 'yam')` → false, but `str_contains('Cocoyam Leaves', 'yam')` → true, incorrectly matching the yam CDN image.  
**Solution:** Reordered the `$map` array to check compound keywords (`kontomire`, `cocoyam`, `watermelon`, `groundnut`, `cowpea`) before simple ones (`yam`, `bean`, `pea`). Added regression unit tests for each case.

### 8.2 Invisible Text on Dashboard Cards
**Challenge:** Multiple views used `bg-primary-container` (#b1f0ce — light mint) with `text-white`, producing < 1.5:1 contrast ratio (WCAG minimum is 4.5:1).  
**Solution:** Systematically audited all views; replaced with `bg-primary` (#2c694e — dark green) which achieves > 7:1 contrast against white. Found in 5 view files.

### 8.3 Unit Test Currency Symbol Mismatch
**Challenge:** Unit tests hardcoded `'₵ 100.00'` (with space) but `CURRENCY_SYMBOL = '₵'` (no space) — caused 4 test failures on first run.  
**Solution:** Removed hardcoded constants from test stubs; loaded real `config.php` to get the authoritative value, then constructed expected strings dynamically.

### 8.4 Session State in Test Environment
**Challenge:** Running unit tests via `php tests/run_tests.php` on the server triggered `session_start()` warnings because the CLI environment has no HTTP headers.  
**Solution:** Wrapped `Session::start()` in a `PHP_SAPI !== 'cli'` guard; test runner now runs cleanly without output buffering issues.

### 8.5 ORDER BY SQL Injection Risk
**Challenge:** The marketplace filter accepts a `sort` GET parameter that was being interpolated directly into the ORDER BY clause.  
**Solution:** Added a `$allowedOrders` whitelist array in `ProduceModel::getAll()`. Only values in the whitelist are accepted; all others default to `p.created_at DESC`.

---

## 9. Testing & Quality Assurance

### 9.1 Unit Test Suite

```
Tests:  43 / 43 passed   ✓  (0 failed, 0 skipped)
Runner: php tests/run_tests.php
```

| Suite | Tests | Coverage |
|---|---|---|
| `Helpers::e()` | 5 | XSS escaping, null safety, Unicode |
| `Helpers::money()` | 4 | Currency formatting, zero, negative, large numbers |
| `Helpers::generateOrderRef()` | 4 | Format, length, uniqueness, prefix |
| `Helpers::paginate()` | 6 | First page, last page, single page, empty |
| `Helpers::matchScore()` | 5 | Each dimension independently + combined |
| `Helpers::statusBadge()` | 7 | All known statuses + unknown fallback |
| `Helpers::sanitize()` | 4 | HTML tags, scripts, Unicode passthrough |
| `Helpers::produceImage()` | 8 | Keyword matches, substring safety, fallback |

### 9.2 Manual Test Plan

49 formal test cases documented in `docs/TEST_PLAN.md` across:
- Authentication (6 TC)
- Farmer Portal (5 TC)
- Buyer Portal (6 TC)
- Transport Portal (3 TC)
- Admin Panel (2 TC)
- Security (4 TC)
- Produce Images (3 TC)
- Unit Tests (20 TC)

### 9.3 Defect Log

| Bug ID | Description | Severity | Status |
|---|---|---|---|
| BUG-001 | Kontomire matched 'yam' keyword (substring) | Medium | ✅ Resolved |
| BUG-002 | Duplicate entries in "Recommended for You" | Low | ✅ Resolved |
| BUG-003 | White text invisible on primary-container cards | Medium | ✅ Resolved |
| BUG-004 | Broken Pexels CDN image IDs | Low | ✅ Resolved |

---

## 10. Key Learnings

### Engineering Insights

1. **Design patterns pay compound interest.** Implementing the Specification/Filter pattern in `ProduceModel::getAll()` from the start meant adding new marketplace filters (e.g., verified-only, price range) required only adding one condition — no structural changes. The upfront cost was ~30 minutes; it saved hours later.

2. **Observer pattern scales notifications at zero cost.** Every business event (order placed, bid accepted, delivery updated) needed a notification. Because `NotificationModel::create()` is a single observer call, adding a new notification type took < 5 minutes per event.

3. **Security must be architectural, not bolted on.** Implementing `Auth::requireRole()` as a single call at the top of each controller, and `Helpers::e()` as the only way to render user data, made it impossible to accidentally bypass either control.

4. **Unit tests expose assumptions.** The `matchScore()` tests initially passed incorrect scores because the test produce had quantity=50 and price=150, accidentally triggering bonus points. Isolating dimensions with controlled test data (qty=5, price=250) made the tests meaningful.

### Process Insights

5. **Sprint retrospectives change behaviour.** The Sprint 1 retrospective identified route complexity in `index.php`. That specific item was addressed before Sprint 2 began — demonstrating the retrospective's concrete value.

6. **Agile scope management works.** Password reset (US-004) was deprioritised from Sprint 1 to Sprint 3 without impacting the platform's core functionality. Deferral was documented, tracked, and delivered as promised.

7. **Documentation as code.** Writing `docs/ARCHITECTURE.md` forced articulation of every architectural decision. Two undocumented assumptions were found and corrected during that process — documentation found bugs that testing did not.

---

## 11. Conclusion

AgriLink Ghana successfully delivers on all eight project objectives. The platform is live, secure, tested, and documented to a standard that demonstrates professional software engineering practice.

The Agile process — with three sprints, 83 story points, four Scrum ceremonies per sprint, and a Definition of Done enforced on every story — provided the structure needed to manage complexity without a framework. The 12 implemented GoF patterns demonstrate that design thinking was applied at every layer, not added retroactively.

The four defects discovered and resolved through systematic testing, combined with a 100% passing unit test suite, confirm that the quality assurance process was genuinely effective.

**Submission artefacts:**
- ✅ Live deployed application — http://169.239.251.102:280/~tomoh.ikfingeh/agrilink/public/
- ✅ GitHub repository — https://github.com/claudetomoh/agrilink
- ✅ `docs/PROJECT_PROPOSAL.md`
- ✅ `docs/ARCHITECTURE.md`
- ✅ `docs/DESIGN_PATTERNS.md`
- ✅ `docs/TEST_PLAN.md`
- ✅ `docs/SPRINT.md`
- ✅ `tests/run_tests.php` — 43/43 tests passing

---

*Submitted for CS 415: Software Engineering — Final Project*  
*AgriLink Ghana Agricultural Marketplace Platform*  
*Tomoh Ikfingeh | April 2026*
