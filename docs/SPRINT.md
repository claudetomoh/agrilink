# AgriLink Ghana — Sprint Documentation

**CS 415: Software Engineering | Final Project**  
**Methodology: Agile (Scrum-lite)**  
**Team Member (Individual Contribution): Tomoh Ikfingeh**  
**Sprint Duration: 2 weeks per sprint**

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
