# AgriLink Ghana — Project Proposal

**CS 415: Software Engineering | Week 3 Submission**  
**Date:** February 2026  
**Team:** AgriLink Development Team  

---

## 1. Problem Statement

Ghana's agricultural sector accounts for approximately 20% of GDP and employs more than half the working population, yet smallholder farmers consistently face three structural barriers:

1. **Market fragmentation** — Farmers sell through intermediaries who capture a disproportionate share of the margin, leaving farmers with as little as 30–40% of the final consumer price.
2. **Information asymmetry** — Buyers lack real-time visibility into regional supply, harvest schedules, and quality grades, leading to spoilage and missed transactions.
3. **Logistics opacity** — No unified platform exists for coordinating farm-gate pickup and last-mile delivery, so buyers and farmers independently negotiate transport on an ad-hoc basis.

These barriers collectively suppress farmer income, inflate buyer costs, and contribute to an estimated 20–30% post-harvest loss rate in Ghana.

---

## 2. Proposed Solution

**AgriLink Ghana** — a multi-role web platform that digitises the agricultural value chain from farm gate to consumer.

The platform connects three actor types in a single ecosystem:
- **Farmers** list produce with pricing, quantity, region, and harvest dates
- **Buyers** browse, filter, search, and order directly from farmers — with a price-bidding mechanism for bulk purchases
- **Transport Providers** see available delivery jobs and manage active routes via a job board
- **Administrators** verify farmer accounts, monitor platform activity, and view aggregate analytics

A machine-learning-inspired **Supply-Demand Matching Engine** scores every listing against a buyer's profile (region, category preference, quantity need, budget) to surface the highest-relevance produce first — reducing search friction.

---

## 3. Objectives

| # | Objective | Success Metric |
|---|---|---|
| O1 | Enable farmers to create and manage produce listings | Farmer can CRUD a listing in < 2 minutes |
| O2 | Enable buyers to discover, filter, and order produce | Buyer can place an order in < 3 minutes |
| O3 | Automate role-based access control | Each role sees only their permitted views |
| O4 | Provide a transport job board for delivery logistics | Transporter can accept/update a delivery |
| O5 | Give administrators full platform visibility | Admin KPI dashboard renders in < 1 second |
| O6 | Implement a matching score engine | Matching page ranks listings by relevance score |
| O7 | Demonstrate professional engineering practices | 12 GoF patterns, 43 unit tests, formal docs |
| O8 | Secure the platform against OWASP Top 10 | SQL injection, XSS, CSRF, session attacks mitigated |

---

## 4. Technologies to be Used

| Layer | Technology | Rationale |
|---|---|---|
| **Language** | PHP 8.0+ | Widely deployed on shared hosting; strong type system in PHP 8 |
| **Architecture** | Custom MVC (no framework) | Demonstrates deep understanding of the pattern |
| **Database** | MySQL 8 via PDO | Relational model fits the marketplace entity relationships |
| **Frontend** | Tailwind CSS + Material Symbols | Utility-first CSS with a design token system for consistency |
| **Version Control** | GitHub | Industry-standard; enables CI, PR reviews, and issue tracking |
| **Project Management** | GitHub Projects (Kanban board) | Native to the repo; tracks issues, user stories, sprint progress |
| **Deployment** | Shared LAMP hosting via SFTP | Matches the university server environment |
| **Testing** | Custom PHP test runner (no PHPUnit dependency) | Standalone; runnable on any PHP 8+ installation |
| **Maps** | OpenStreetMap / Leaflet.js embed | Open-source; no API key required |
| **CDN Images** | Pexels API | High-quality produce imagery without local storage |

---

## 5. Expected Outcomes

By the end of the project, the team expects to deliver:

1. **A live, deployed web application** accessible at a public URL, demonstrating all four portals (Farmer, Buyer, Transport, Admin)
2. **A functional matching engine** that scores listings against buyer profiles using a multi-dimensional scoring algorithm
3. **A secure platform** with mitigations for SQL injection (parameterised queries), XSS (output escaping), CSRF (token validation), and session fixation (ID regeneration on login)
4. **Formal engineering documentation**: Architecture document, Design Patterns catalogue (12 patterns), Test Plan (49 test cases), and Sprint documentation
5. **An executable unit test suite** with 100% pass rate (43/43 tests)
6. **A GitHub repository** with a structured commit history demonstrating iterative development across 3 sprints

---

## 6. Team Structure & Scrum Roles

| Role | Member | Responsibilities |
|---|---|---|
| **Product Owner** | Tomoh Ikfingeh | Maintains product backlog, defines user stories, accepts or rejects sprint output |
| **Scrum Master** | Tomoh Ikfingeh | Facilitates sprint ceremonies, removes blockers, ensures Scrum adherence |
| **Lead Developer** | Tomoh Ikfingeh | Core MVC architecture, security layer, matching engine, test suite |
| **Frontend Developer** | *(Team Member — update with name)* | View templates, CSS design tokens, responsive layouts |
| **Backend Developer** | *(Team Member — update with name)* | Database schema, model layer, REST-style routing |
| **QA / Documentation** | *(Team Member — update with name)* | Test plan authoring, sprint documentation, defect logging |

> **Note:** As the individual contributor for CS 415, Tomoh Ikfingeh took on all engineering roles for this submission. The role assignments above reflect the intended split in a full-team context.

---

## 7. Proposed Timeline

| Week | Milestone |
|---|---|
| 1–2 | Architecture design, database schema, project setup |
| 3 | **Project Proposal submission** |
| 3–4 | Sprint 1: MVC skeleton, authentication, RBAC, security layer |
| 5–6 | Sprint 2: All four portals, order management, bidding, notifications |
| 7–8 | Sprint 3: Matching engine, analytics, testing, documentation |
| 9–13 | Buffer / iterations, peer review, polish |
| 14 | **Final Submission** |
| 15 | **Presentation & Live Demo** |

---

*Submitted for CS 415: Software Engineering — AgriLink Ghana Agricultural Marketplace Platform*  
*February 2026*
