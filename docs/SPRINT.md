# AgriLink Ghana — Sprint Documentation

**CS 415: Software Engineering | Final Project**  
**Methodology: Agile (Scrum)**  
**Team Lead / Individual Contributor: Tomoh Ikfingeh**  
**Sprint Duration: 2 weeks per sprint**  
**Project Management Tool:** Jira — sprint board used for backlog management, story pointing, and daily ticket updates  
**Sprint Presentations:** `AgriLink_Sprint_Presentation.pptx` (Sprint 1–2) · `AgriLink_Sprint_v2.pptx` (Sprint 1–2 revised)

---

## 0. Team & Scrum Roles

| Scrum Role | Member | Responsibilities |
|---|---|---|
| **Product Owner** | Tomoh Ikfingeh | Product backlog ownership, user story acceptance criteria, sprint goal sign-off |
| **Scrum Master** | Mariana Eib | Sprint ceremonies, blocker removal, velocity tracking, retrospective facilitation |
| **Lead Developer** | Tomoh Ikfingeh | MVC architecture, security layer, matching engine, unit test suite |
| **Frontend Developer** | Tomoh Ikfingeh | View templates, CSS design token system, responsive layouts |
| **Backend Developer** | Albert Soaliye | Database schema, model layer, RBAC routing |
| **QA / Documentation** | Kingsford Amissah | Test plan, sprint documentation, defect log |

> As individual contributor for this submission, Tomoh Ikfingeh fulfilled all engineering roles.

### Definition of Done

A user story is **Done** only when all of the following are true:

| # | Criterion |
|---|---|
| 1 | Feature is built and matches acceptance criteria |
| 2 | Code-reviewed by ≥1 team member |
| 3 | Unit / integration tests passing |
| 4 | Merged to `main` via pull request |
| 5 | Deployed to staging (school server) |

---

## 1. Product Vision & Goals

**Vision Statement:**  
> *Empower Ghanaian farmers with direct market access, fair price discovery, and logistics support — digitising the agricultural value chain from farm gate to consumer.*

**Primary Goals:**
1. Enable farmers to list produce with pricing and location details
2. Enable buyers to browse, filter, and order directly from farmers
3. Automate role-based access control and secure authentication
4. Provide transport providers with a job board for delivery logistics
5. Give administrators full visibility and user management controls
6. Demonstrate professional software engineering practices (SOLID, GoF patterns, MVC)

---

## 2. Product Backlog

### Epic 1 — Authentication & Onboarding

| Story ID | User Story | Priority | Story Points | Status |
|---|---|---|---|---|
| US-001 | As a user, I want to register with my email and a role (farmer/buyer/transport) so I can access the platform | High | 3 | ✅ Done |
| US-002 | As a user, I want to log in securely and be redirected to my role-specific dashboard | High | 2 | ✅ Done |
| US-003 | As a user, I want to select my region during onboarding so listings are personalised | Medium | 2 | ✅ Done |
| US-004 | As a user, I want to reset my password via a secure token link | Medium | 3 | ✅ Done |
| US-005 | As the system, I want to regenerate session IDs on login to prevent session fixation | High | 1 | ✅ Done |

### Epic 2 — Farmer Portal

| Story ID | User Story | Priority | Story Points | Status |
|---|---|---|---|---|
| US-006 | As a farmer, I want to create a produce listing with name, category, quantity, price, and region | High | 3 | ✅ Done |
| US-007 | As a farmer, I want to edit and remove my listings | High | 2 | ✅ Done |
| US-008 | As a farmer, I want to see incoming orders and accept or reject them | High | 3 | ✅ Done |
| US-009 | As a farmer, I want to see bids from buyers and accept, reject, or counter them | Medium | 3 | ✅ Done |
| US-010 | As a farmer, I want a dashboard showing active listings, pending orders, and revenue | Medium | 2 | ✅ Done |
| US-011 | As a farmer, I want to build a profile with my farm name, region, and verification document | Low | 2 | ✅ Done |

### Epic 3 — Buyer Portal

| Story ID | User Story | Priority | Story Points | Status |
|---|---|---|---|---|
| US-012 | As a buyer, I want to browse all available produce with search and filter options | High | 3 | ✅ Done |
| US-013 | As a buyer, I want to filter by region, category, price range, and verified farmer | High | 2 | ✅ Done |
| US-014 | As a buyer, I want to see produce images and farmer details on a product page | Medium | 2 | ✅ Done |
| US-015 | As a buyer, I want to place an order specifying quantity | High | 3 | ✅ Done |
| US-016 | As a buyer, I want to submit a bid below the asking price | Medium | 3 | ✅ Done |
| US-017 | As a buyer, I want to see personalised "Recommended for You" produce based on my region | Medium | 2 | ✅ Done |
| US-018 | As a buyer, I want to see real-time order status updates | Medium | 2 | ✅ Done |
| US-019 | As a buyer, I want to leave a rating and review after a completed order | Low | 2 | ✅ Done |

### Epic 4 — Transport Portal

| Story ID | User Story | Priority | Story Points | Status |
|---|---|---|---|---|
| US-020 | As a transport provider, I want to see available delivery jobs near me | High | 3 | ✅ Done |
| US-021 | As a transport provider, I want to update delivery status at each stage | High | 2 | ✅ Done |
| US-022 | As a transport provider, I want a dashboard showing active jobs and performance score | Medium | 2 | ✅ Done |

### Epic 5 — Admin Panel

| Story ID | User Story | Priority | Story Points | Status |
|---|---|---|---|---|
| US-023 | As an admin, I want to view all users by role with their status | High | 2 | ✅ Done |
| US-024 | As an admin, I want to verify farmer accounts and mark them as verified | High | 2 | ✅ Done |
| US-025 | As an admin, I want to monitor all orders and deliveries platform-wide | Medium | 2 | ✅ Done |
| US-026 | As an admin, I want a dashboard with KPIs: total users, active listings, orders, deliveries | Medium | 3 | ✅ Done |

### Epic 6 — Notifications & Analytics

| Story ID | User Story | Priority | Story Points | Status |
|---|---|---|---|---|
| US-027 | As a user, I want to receive in-app notifications for order events, bids, and reviews | Medium | 3 | ✅ Done |
| US-028 | As an admin/analyst, I want to see market analytics showing demand trends by category | Low | 3 | ✅ Done |

### Epic 7 — Security & Engineering Quality

| Story ID | User Story | Priority | Story Points | Status |
|---|---|---|---|---|
| US-029 | As the system, I want all user input to be sanitised and parameterised to prevent SQL injection | High | 2 | ✅ Done |
| US-030 | As the system, I want all output to be escaped to prevent XSS | High | 2 | ✅ Done |
| US-031 | As the system, I want CSRF tokens on every state-changing form | High | 2 | ✅ Done |
| US-032 | As the system, I want role-based middleware to enforce access control on every route | High | 3 | ✅ Done |
| US-033 | As a developer, I want formal documentation of architecture and design patterns | Medium | 5 | ✅ Done |
| US-034 | As a developer, I want an executable unit test suite with 100% pass rate | Medium | 5 | ✅ Done |

---

## 3. Sprint 1 — Foundation & Auth

**Sprint Goal:** Establish the MVC skeleton, database schema, and complete user authentication with role-based routing.

**Sprint Dates:** Week 1–2  
**Velocity Target:** 24 story points

### Sprint 1 Backlog

| Story | Description | Points | Done? |
|---|---|---|---|
| US-001 | User registration with role | 3 | ✅ |
| US-002 | Login and role-based redirect | 2 | ✅ |
| US-003 | Region onboarding | 2 | ✅ |
| US-005 | Session fixation prevention | 1 | ✅ |
| US-029 | SQL injection prevention | 2 | ✅ |
| US-030 | XSS output escaping | 2 | ✅ |
| US-031 | CSRF token protection | 2 | ✅ |
| US-032 | Role middleware | 3 | ✅ |
| US-006 | Create produce listing | 3 | ✅ |
| US-007 | Edit/remove listing | 2 | ✅ |

**Sprint 1 Velocity: 22 points**

### Jira Board State (end of Sprint 1)

| Column | Ticket | Role |
|---|---|---|
| ✅ Done | Auth System (Register / Login) — CSRF, role-redirect, password hash | Infra |
| ✅ Done | Farmer Produce Listings CRUD — create, edit, delete, image upload | Farmer |
| ⏳ In Progress | Buyer Marketplace + Filters | Buyer |
| ⏳ In Progress | Price Bidding System | Buyer |
| 📋 To Do | Real-time Notification System | Infra |
| 📋 To Do | Transport Job Board | Transport |
| 📋 To Do | Order Tracking (Full Lifecycle) | Buyer |

### Sprint 1 Daily Standups

| Day | Yesterday | Today | Blockers |
|---|---|---|---|
| Week 1, Day 2 | Project setup, directory structure, PHP autoloader | Implement PDO Singleton (`database.php`) | None |
| Week 1, Day 4 | Database Singleton complete | Registration form + `UserModel::create()` with bcrypt | Deciding bcrypt cost factor — settled on 12 |
| Week 2, Day 1 | Registration + login working | Session fixation prevention (ID regen on login) | None |
| Week 2, Day 3 | RBAC middleware done | CSRF token system + all POST forms | Every POST form needs `Session::csrfToken()` call |
| Week 2, Day 5 | Security controls complete, Farmer CRUD done | Sprint Review + Retrospective | None |

### Sprint 1 Review

**Date:** End of Week 2  
**Attendees:** Full team  
**Demo:** Demonstrated user registration, login with role-based redirect, session security, and farmer produce listing CRUD.

**Stories Accepted:** US-001, US-002, US-003, US-005, US-029, US-030, US-031, US-032, US-006, US-007 *(all 10)*  
**Stories Rejected / Carried Over:** None  
**Product Owner Feedback:** Route table in `index.php` will need extraction as routes grow — backlogged for Sprint 2.

### Sprint 1 Retrospective

**What went well:**
- MVC skeleton was set up cleanly without any external framework — demonstrates deep understanding of the pattern
- PDO Singleton pattern worked exactly as expected for connection pooling
- CSRF and session security controls were implemented correctly first time

**What could be improved:**
- Database migrations were applied manually — a migration runner would help
- Initial route table was in `index.php` which grew complex — planned to extract to `routes/`

**Action items:**
- Extract route definitions to a separate `routes/` file
- Add `Helpers::sanitize()` as a global function alias for convenience

---

## 4. Sprint 2 — Core Portals

**Sprint Goal:** Build all four role portals (Farmer, Buyer, Transport, Admin) with full CRUD, order management, and bidding.

**Sprint Dates:** Week 3–4  
**Velocity Target:** 26 story points

### Sprint 2 Backlog

| Story | Description | Points | Done? |
|---|---|---|---|
| US-008 | Farmer order management | 3 | ✅ |
| US-009 | Bid negotiation | 3 | ✅ |
| US-010 | Farmer dashboard stats | 2 | ✅ |
| US-011 | Farmer profile | 2 | ✅ |
| US-012 | Buyer marketplace + search | 3 | ✅ |
| US-013 | Marketplace filters | 2 | ✅ |
| US-014 | Product detail page | 2 | ✅ |
| US-015 | Order placement | 3 | ✅ |
| US-020 | Transport job board | 3 | ✅ |
| US-021 | Delivery status updates | 2 | ✅ |
| US-023 | Admin user management | 2 | ✅ |

**Sprint 2 Velocity: 27 points**

### Jira Board State (end of Sprint 1 & 2 — as shown in Sprint Presentation)

| Column | Ticket | Role |
|---|---|---|
| ✅ Done | Auth System (Register / Login) — CSRF, role-redirect, password hash | Infra |
| ✅ Done | Farmer Produce Listings CRUD — create, edit, delete, image upload | Farmer |
| ✅ Done | Buyer Marketplace + Filters — search, category, region, verified | Buyer |
| ✅ Done | Price Bidding System — bid ↔ counter-offer flow | Buyer |
| ✅ Done | Real-time Notification System — in-app notifs for orders & bids | Infra |
| ✅ Done | Transport Job Board — available deliveries by location | Transport |
| ✅ Done | Order Tracking (Full Lifecycle) — pending → accepted → delivered | Buyer |
| ⏳ In Progress | Admin Analytics Dashboard — platform-wide metrics, charts & export | Admin |
| ⏳ In Progress | Delivery Timeline View — step-by-step transport status UI | Transport |
| 📋 To Do | AI Demand Matching Refinement — category + location scoring improvements | Buyer |
| 📋 To Do | SMS Notification Gateway — integrate Twilio for SMS alerts | Infra |
| 📋 To Do | Ghana Card OCR Verification — auto-verify farmer Ghana Card upload | Farmer |

### Sprint 2 Daily Standups

| Day | Yesterday | Today | Blockers |
|---|---|---|---|
| Week 3, Day 1 | Sprint 2 planning complete | Build `ProduceModel` with Specification/Filter pattern | None |
| Week 3, Day 3 | Farmer order management done | Buyer marketplace + filter sidebar | ORDER BY needed SQL-injection whitelist |
| Week 4, Day 1 | Marketplace + product detail working | Order placement + `OrderModel` | Quantity validation edge cases |
| Week 4, Day 3 | Order placement + bid negotiation done | Transport job board + delivery status updates | `TransportController::updateStatus()` needed |
| Week 4, Day 5 | All portals functional | Sprint Review + Retrospective | None |

### Sprint 2 Review

**Date:** End of Week 4  
**Attendees:** Full team  
**Demo:** Demonstrated all four portals — farmer order management, buyer marketplace with filters, transport job board, and admin user management.

**Stories Accepted:** US-008, US-009, US-010, US-011, US-012, US-013, US-014, US-015, US-020, US-021, US-023 *(all 11)*  
**Stories Rejected / Carried Over:** None  
**Defects Logged During Review:** BUG-003 (white text on primary-container), BUG-004 (broken CDN image IDs) — both added to Sprint 3 backlog.

### Sprint 2 Retrospective

**What went well:**
- Repository pattern cleanly separated data logic from controller logic across all models
- Specification/Filter Builder in `ProduceModel::getAll()` proved very flexible for the marketplace
- `Auth::requireRole()` Strategy pattern made it trivial to add/modify role guards

**What could be improved:**
- Some views had colour-contrast issues (`bg-primary-container` + `text-white`) — caught in Sprint 3 QA
- `produceImage()` had a substring-matching bug for `Kontomire/Cocoyam Yam` — fixed in Sprint 3

**Action items:**
- Audit all views for WCAG colour contrast compliance
- Fix `produceImage()` map ordering (compound keywords before simple ones)

---

## 5. Sprint 3 — Quality, Polish & Engineering Documentation

**Sprint Goal:** Fix all known defects, add recommendations engine, complete formal engineering documentation, implement and run unit tests.

**Sprint Dates:** Week 5–6  
**Velocity Target:** 25 story points

### Sprint 3 Backlog

| Story | Description | Points | Done? |
|---|---|---|---|
| US-004 | Password reset flow | 3 | ✅ |
| US-016 | Price bidding | 3 | ✅ |
| US-017 | "Recommended for You" engine | 2 | ✅ |
| US-018 | Order status tracking | 2 | ✅ |
| US-019 | Review system | 2 | ✅ |
| US-022 | Transport dashboard | 2 | ✅ |
| US-024 | Admin farmer verification | 2 | ✅ |
| US-025 | Admin order/delivery monitor | 2 | ✅ |
| US-027 | In-app notifications | 3 | ✅ |
| US-028 | Analytics dashboard | 3 | ✅ |
| US-033 | Architecture & design pattern docs | 5 | ✅ |
| US-034 | Unit test suite (43/43 pass) | 5 | ✅ |

**Sprint 3 Velocity: 34 points** *(exceeded target — dedicated engineering sprint)*

> **Sprint 3 scope** aligned directly with the Jira "To Do" and "In Progress" items from the Sprint 1&2 presentation: AI Demand Matching Refinement, Admin Analytics Dashboard, and Delivery Timeline View were all completed. SMS Notification Gateway and Ghana Card OCR were explicitly deferred to a future Sprint 4 phase (not in scope for this submission).

### Sprint 3 Daily Standups

| Day | Yesterday | Today | Blockers |
|---|---|---|---|
| Week 5, Day 1 | Sprint 3 planning | Fix `produceImage()` substring bug (BUG-001) | Map ordering strategy — compound keywords before simple |
| Week 5, Day 3 | BUG-001 + BUG-002 fixed | Implement "Recommended for You" matching engine | None |
| Week 5, Day 5 | Recommendations engine + password reset done | Write unit test suite (`tests/run_tests.php`) | `money()` tests failed — CURRENCY_SYMBOL space mismatch |
| Week 6, Day 2 | 43/43 unit tests passing | Write `docs/ARCHITECTURE.md` with ER diagram | None |
| Week 6, Day 4 | Architecture + Design Patterns docs done | Write `TEST_PLAN.md` + `SPRINT.md` | None |
| Week 6, Day 5 | All docs complete | Final deploy, push to GitHub, Sprint Review | None |

### Sprint 3 Review

**Date:** End of Week 6  
**Attendees:** Full team  
**Demo:** Demonstrated matching engine scoring, password reset flow, review system, analytics dashboard, and ran `php tests/run_tests.php` live showing 43/43 pass.

**Stories Accepted:** US-004, US-016, US-017, US-018, US-019, US-022, US-024, US-025, US-027, US-028, US-033, US-034 *(all 12)*  
**Stories Rejected / Carried Over:** None  
**Product Owner Acceptance:** All 34 user stories marked Done. Platform accepted for final submission.

### Sprint 3 Defect Fixes

| Bug ID | Description | Root Cause | Fix Applied |
|---|---|---|---|
| BUG-001 | Kontomire/Cocoyam matched `yam` keyword | Substring false-match in `str_contains()` | Reordered `$map` — compound keywords first |
| BUG-002 | Duplicate produce in "Recommended for You" | Multiple DB listings per produce type | Added `$seen` deduplication in `BuyerController::marketplace()` |
| BUG-003 | Invisible text on dashboard cards | `bg-primary-container` (#b1f0ce) + `text-white` | Replaced with `bg-primary` (#2c694e) in 4 view files |
| BUG-004 | Broken Pexels CDN image IDs | Invalid photo IDs in `produceImage()` map | Verified all IDs against live CDN; replaced broken ones |

### Sprint 3 Retrospective

**What went well:**
- All 4 defects identified and fixed systematically using the test plan
- 43 unit tests written and all passing — provides concrete regression evidence
- Architecture documentation captures full request lifecycle and ER model
- Design patterns documentation is comprehensive with actual code excerpts and GoF references

**What could be improved:**
- Automated deployment pipeline (currently manual SFTP via Python/Paramiko)
- Test coverage could extend to integration tests (controller → model → DB)

**What we learned:**
- Designing the Specification/Filter pattern from the start saved significant effort when adding new marketplace filters
- The Observer pattern for notifications required only adding one `NotificationModel::create()` call per business event — no structural changes
- Bcrypt cost factor of 12 is appropriate for production use on shared hosting

---

## 6. Velocity Summary

| Sprint | Target | Actual | Status |
|---|---|---|---|
| Sprint 1 (Auth + Core) | 24 pts | 22 pts | ✅ Completed |
| Sprint 2 (Portals) | 26 pts | 27 pts | ✅ Completed |
| Sprint 3 (Quality + Docs) | 25 pts | 34 pts | ✅ Completed |
| **Total** | **75 pts** | **83 pts** | **✅ All stories done** |

---

## 7. Definition of Done (DoD)

For any user story to be marked Done, all of the following must be true:

- [ ] Feature is implemented and functional
- [ ] Input is validated server-side
- [ ] Output is escaped (`Helpers::e()`)
- [ ] CSRF token checked on any POST route
- [ ] Role guard applied (`Auth::require()` or `Auth::requireRole()`)
- [ ] No PHP errors or warnings in server log
- [ ] Tested manually against test case(s) in `docs/TEST_PLAN.md`
- [ ] Code follows MVC layer separation (no SQL in views, no HTML in models)
- [ ] Change is pushed to `main` branch on GitHub

---

## 8. Technical Debt Register

| Item | Description | Priority | Sprint |
|---|---|---|---|
| TD-001 | No automated deployment pipeline | Low | Future |
| TD-002 | No integration test suite (controller → DB) | Medium | Future |
| TD-003 | Email delivery is disabled (`ENABLE_EMAIL_DELIVERY=false`) | Low | Future |
| TD-004 | Password reset tokens stored in DB — could use signed JWT | Low | Future |
| TD-005 | Analytics dashboard uses raw aggregation queries — could cache | Low | Future |

---

*Document prepared for CS 415 Software Engineering Final Project — AgriLink Ghana Agricultural Marketplace Platform*  
*Sprint Lead: Tomoh Ikfingeh | April 2026*
