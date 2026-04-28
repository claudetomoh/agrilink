# AgriLink Ghana — Test Plan

**CS 415: Software Engineering | Final Project Documentation**  
**Module: Testing & Quality Assurance**

---

## 1. Test Strategy Overview

### 1.1 Purpose

This document defines the testing approach for AgriLink Ghana — an agricultural marketplace platform serving Ghanaian farmers, buyers, transport providers, and administrators. It covers the test scope, types of testing employed, individual test cases, acceptance criteria, and test results.

### 1.2 Testing Objectives

- Verify that each functional requirement is correctly implemented.
- Validate that non-functional requirements (security, usability, reliability) are met.
- Confirm that role-based access control prevents unauthorised access.
- Demonstrate that the system is stable for the final demonstration.

### 1.3 Test Types Used

| Type | Description | Tools |
|---|---|---|
| **Unit Testing** | Test individual PHP functions and class methods in isolation | Custom PHP test runner |
| **Integration Testing** | Test that controllers, models, and database interact correctly | Live test scripts |
| **System Testing** | End-to-end functional test of complete user journeys | Manual + browser |
| **Security Testing** | Verify XSS, CSRF, SQL injection, and auth controls | Manual test cases |
| **User Acceptance Testing** | Verify the system meets user needs | Walkthrough against requirements |
| **Regression Testing** | Verify bug fixes do not break existing features | Re-run test cases after changes |

### 1.4 Definition of Done for a Test

A feature is considered **tested and verified** when:
- The test case has defined pre-conditions
- Test steps have been executed
- The actual result matches the expected result
- Result is recorded (Pass / Fail)
- Any failures are logged and addressed

---

## 2. Test Environment

| Component | Detail |
|---|---|
| **Server** | 169.239.251.102:280 (Apache) |
| **Database** | MySQL — `mobileapps_2026B_tomoh_ikfingeh` |
| **PHP Version** | 8.0+ |
| **Test Browser** | Chromium (Playwright) |
| **Test Accounts** | See Section 2.1 |

### 2.1 Test Accounts

| Role | Email | Password |
|---|---|---|
| Farmer | kofi.boateng@agrilink.gh | Pass@1234 |
| Buyer | kwame.mensah@agrilink.gh | Pass@1234 |
| Transport | kojo.logistics@agrilink.gh | Pass@1234 |
| Admin | admin@agrilink.gh | Admin@1234 |

---

## 3. Test Cases — Authentication

### TC-AUTH-01: Successful Login (Farmer)

| Field | Detail |
|---|---|
| **Test ID** | TC-AUTH-01 |
| **Feature** | User Login |
| **Pre-conditions** | Account `kofi.boateng@agrilink.gh` exists with role=farmer |
| **Test Steps** | 1. Navigate to `/login` |
| | 2. Enter email: `kofi.boateng@agrilink.gh` |
| | 3. Enter password: `Pass@1234` |
| | 4. Click "Login" |
| **Expected Result** | Redirected to `/farmer/dashboard`. Dashboard renders with farmer's name. |
| **Actual Result** | ✅ PASS — Redirected to farmer dashboard. Kofi Boateng dashboard loads correctly. |

---

### TC-AUTH-02: Invalid Password Rejected

| Field | Detail |
|---|---|
| **Test ID** | TC-AUTH-02 |
| **Feature** | Login Validation |
| **Pre-conditions** | None |
| **Test Steps** | 1. Navigate to `/login` |
| | 2. Enter valid email with wrong password: `wrongpass` |
| | 3. Click "Login" |
| **Expected Result** | Stays on login page. Flash error: "Invalid credentials." No session created. |
| **Actual Result** | ✅ PASS — Error message shown, no redirect, session not created. |

---

### TC-AUTH-03: Role Redirect After Login

| Field | Detail |
|---|---|
| **Test ID** | TC-AUTH-03 |
| **Feature** | Role-based redirect |
| **Pre-conditions** | Not logged in |
| **Test Steps** | Login with buyer account, then admin account separately |
| **Expected Result** | Buyer → `/buyer/marketplace`. Admin → `/admin/dashboard`. |
| **Actual Result** | ✅ PASS — Each role redirected to correct dashboard. |

---

### TC-AUTH-04: Farmer Cannot Access Admin Panel

| Field | Detail |
|---|---|
| **Test ID** | TC-AUTH-04 |
| **Feature** | Role-based access control |
| **Pre-conditions** | Logged in as farmer |
| **Test Steps** | 1. Login as farmer |
| | 2. Manually navigate to `/admin/dashboard` |
| **Expected Result** | HTTP 403 response. Access denied page shown. No admin data visible. |
| **Actual Result** | ✅ PASS — 403 page shown. Admin data not accessible. |

---

### TC-AUTH-05: Unauthenticated Access Redirects to Login

| Field | Detail |
|---|---|
| **Test ID** | TC-AUTH-05 |
| **Feature** | Authentication guard |
| **Pre-conditions** | Not logged in |
| **Test Steps** | Navigate to `/farmer/dashboard` without logging in |
| **Expected Result** | Redirect to `/login`. Flash message: "Please log in to access that page." |
| **Actual Result** | ✅ PASS — Redirect with flash message shown. |

---

### TC-AUTH-06: Registration With Duplicate Email Rejected

| Field | Detail |
|---|---|
| **Test ID** | TC-AUTH-06 |
| **Feature** | Registration validation |
| **Pre-conditions** | `kofi.boateng@agrilink.gh` already registered |
| **Test Steps** | 1. Navigate to `/register` |
| | 2. Submit registration with email `kofi.boateng@agrilink.gh` |
| **Expected Result** | Form rejected. Error: "An account with this email already exists." |
| **Actual Result** | ✅ PASS — Duplicate email validation works. |

---

## 4. Test Cases — Farmer Portal

### TC-FARM-01: Create Produce Listing

| Field | Detail |
|---|---|
| **Test ID** | TC-FARM-01 |
| **Feature** | Add new produce listing |
| **Pre-conditions** | Logged in as farmer |
| **Test Steps** | 1. Navigate to `/farmer/listings/add` |
| | 2. Fill: Name="Fresh Yam", Category="Tubers", Qty=50 bags, Price=₵85/bag, Region=Ashanti |
| | 3. Submit form |
| **Expected Result** | Listing created. Redirect to `/farmer/listings`. New listing appears in table with status "Available". |
| **Actual Result** | ✅ PASS — Listing created and visible in farmer listings. |

---

### TC-FARM-02: Listing Validation — Zero Quantity Rejected

| Field | Detail |
|---|---|
| **Test ID** | TC-FARM-02 |
| **Feature** | Input validation |
| **Pre-conditions** | Logged in as farmer |
| **Test Steps** | Submit add listing form with Quantity = 0 |
| **Expected Result** | Form rejected. Error: "Quantity must be greater than zero." |
| **Actual Result** | ✅ PASS — Validation error shown, listing not created. |

---

### TC-FARM-03: Accept Order Updates Status

| Field | Detail |
|---|---|
| **Test ID** | TC-FARM-03 |
| **Feature** | Order management |
| **Pre-conditions** | A pending order exists for the farmer's listing |
| **Test Steps** | 1. Navigate to `/farmer/orders` |
| | 2. Click "Confirm" on a pending order |
| **Expected Result** | Order status changes to "Confirmed". Buyer receives notification. |
| **Actual Result** | ✅ PASS — Order status updated. Notification created for buyer. |

---

### TC-FARM-04: Farmer Dashboard Shows Correct Stats

| Field | Detail |
|---|---|
| **Test ID** | TC-FARM-04 |
| **Feature** | Dashboard statistics |
| **Pre-conditions** | Farmer has active listings and completed orders |
| **Test Steps** | Navigate to `/farmer/dashboard` |
| **Expected Result** | Cards show correct count of active listings, pending orders, and correct total revenue (sum of delivered + completed orders). |
| **Actual Result** | ✅ PASS — Stats calculated correctly from database. |

---

### TC-FARM-05: CSRF Token Required on Form Submission

| Field | Detail |
|---|---|
| **Test ID** | TC-FARM-05 |
| **Feature** | CSRF protection |
| **Pre-conditions** | Logged in as farmer |
| **Test Steps** | 1. Submit add-listing POST request with `_token` field removed or tampered |
| **Expected Result** | Request rejected. Flash error: "Invalid request token." Form not processed. |
| **Actual Result** | ✅ PASS — CSRF validation rejects tampered submissions. |

---

## 5. Test Cases — Buyer Portal

### TC-BUY-01: Marketplace Loads All Available Listings

| Field | Detail |
|---|---|
| **Test ID** | TC-BUY-01 |
| **Feature** | Marketplace listing |
| **Pre-conditions** | Available produce listings exist |
| **Test Steps** | 1. Login as buyer |
| | 2. Navigate to `/buyer/marketplace` |
| **Expected Result** | Page loads. Available produce cards displayed. No archived/sold items shown. Images load correctly. |
| **Actual Result** | ✅ PASS — Marketplace loads. Correct produce images via CDN. |

---

### TC-BUY-02: Search Filter Returns Correct Results

| Field | Detail |
|---|---|
| **Test ID** | TC-BUY-02 |
| **Feature** | Marketplace search |
| **Pre-conditions** | Listings exist including "Maize" items |
| **Test Steps** | 1. In search box, type "Maize" |
| | 2. Submit search |
| **Expected Result** | Only listings matching "Maize" in name or description shown. Unrelated produce not shown. |
| **Actual Result** | ✅ PASS — Search filter returns only matching results. |

---

### TC-BUY-03: Place Order Successfully

| Field | Detail |
|---|---|
| **Test ID** | TC-BUY-03 |
| **Feature** | Order placement |
| **Pre-conditions** | An available listing exists with quantity ≥ 5 bags |
| **Test Steps** | 1. Navigate to product detail page |
| | 2. Enter quantity: 5 |
| | 3. Click "Place Order" |
| **Expected Result** | Order created. Redirect to orders page. New order with unique reference (AL-XXXXXX) shown as "Pending". |
| **Actual Result** | ✅ PASS — Order placed. Unique reference generated. Order appears in buyer orders. |

---

### TC-BUY-04: Recommended Items Are Unique (No Duplicates)

| Field | Detail |
|---|---|
| **Test ID** | TC-BUY-04 |
| **Feature** | "Recommended for You" section |
| **Pre-conditions** | Multiple listings of the same produce type exist in buyer's region |
| **Test Steps** | 1. Login as buyer with region set to Ashanti |
| | 2. Navigate to `/buyer/marketplace` |
| | 3. Inspect "Recommended for You" section |
| **Expected Result** | Each produce type appears at most once. No duplicate produce names in recommendations. |
| **Actual Result** | ✅ PASS — Deduplication by produce name applied. Kontomire, Cowpea each appear once. |

---

### TC-BUY-05: Order Quantity Exceeding Stock Rejected

| Field | Detail |
|---|---|
| **Test ID** | TC-BUY-05 |
| **Feature** | Order validation |
| **Pre-conditions** | A listing exists with quantity = 10 bags |
| **Test Steps** | Attempt to place order for 15 bags |
| **Expected Result** | Order rejected. Error: "Invalid order quantity." |
| **Actual Result** | ✅ PASS — Quantity validation enforced. |

---

### TC-BUY-06: Submit Price Bid

| Field | Detail |
|---|---|
| **Test ID** | TC-BUY-06 |
| **Feature** | Price bidding |
| **Pre-conditions** | Available listing exists. Buyer logged in. |
| **Test Steps** | 1. Navigate to product detail |
| | 2. Enter bid amount lower than asking price |
| | 3. Submit bid |
| **Expected Result** | Bid created with status "Pending". Farmer receives "New Bid Received" notification. |
| **Actual Result** | ✅ PASS — Bid created. Farmer notification created. |

---

## 6. Test Cases — Transport Portal

### TC-TRANS-01: Transport Dashboard Loads

| Field | Detail |
|---|---|
| **Test ID** | TC-TRANS-01 |
| **Feature** | Transport dashboard |
| **Pre-conditions** | Logged in as transport user |
| **Test Steps** | Navigate to `/transport/dashboard` |
| **Expected Result** | Dashboard loads. Active deliveries, job stats, and performance score visible. |
| **Actual Result** | ✅ PASS — Dashboard renders correctly. |

---

### TC-TRANS-02: Transport Cannot Access Buyer Marketplace

| Field | Detail |
|---|---|
| **Test ID** | TC-TRANS-02 |
| **Feature** | Role isolation |
| **Pre-conditions** | Logged in as transport user |
| **Test Steps** | Navigate to `/buyer/marketplace` |
| **Expected Result** | 403 Access Denied. Buyer marketplace not accessible. |
| **Actual Result** | ✅ PASS — Role isolation enforced. |

---

### TC-TRANS-03: Update Delivery Status

| Field | Detail |
|---|---|
| **Test ID** | TC-TRANS-03 |
| **Feature** | Delivery status update |
| **Pre-conditions** | An assigned delivery job exists |
| **Test Steps** | 1. Navigate to `/transport/jobs` |
| | 2. Click "Mark as Picked Up" on a job |
| **Expected Result** | Delivery status updates. Timeline reflects change. Buyer notified. |
| **Actual Result** | ✅ PASS — Status updated and buyer notification created. |

---

## 7. Test Cases — Admin Panel

### TC-ADM-01: Admin Dashboard Shows Platform Statistics

| Field | Detail |
|---|---|
| **Test ID** | TC-ADM-01 |
| **Feature** | Admin KPI dashboard |
| **Pre-conditions** | Logged in as admin |
| **Test Steps** | Navigate to `/admin/dashboard` |
| **Expected Result** | Dashboard loads. Total Users, Active Listings, Total Orders, Deliveries in Transit shown as accurate counts. |
| **Actual Result** | ✅ PASS — Dashboard loads with correct stats. |

---

### TC-ADM-02: Admin Can View All Users

| Field | Detail |
|---|---|
| **Test ID** | TC-ADM-02 |
| **Feature** | User management |
| **Pre-conditions** | Logged in as admin |
| **Test Steps** | Navigate to `/admin/users` |
| **Expected Result** | All users across all roles listed. Farmer, buyer, transport, admin accounts visible. |
| **Actual Result** | ✅ PASS — All users listed with correct role badges. |

---

## 8. Test Cases — Security

### TC-SEC-01: SQL Injection Blocked

| Field | Detail |
|---|---|
| **Test ID** | TC-SEC-01 |
| **Feature** | SQL injection prevention |
| **Test Steps** | Submit `' OR '1'='1` as the search query in marketplace search |
| **Expected Result** | No SQL error. No unexpected data returned. Search returns zero results. |
| **Actual Result** | ✅ PASS — Parameterised query handles injection attempt safely. |

---

### TC-SEC-02: XSS Output Escaping

| Field | Detail |
|---|---|
| **Test ID** | TC-SEC-02 |
| **Feature** | XSS prevention |
| **Test Steps** | Create a listing with name `<script>alert('xss')</script>` |
| **Expected Result** | Name stored as plain text. Rendered escaped as `&lt;script&gt;...` in browser. No alert fires. |
| **Actual Result** | ✅ PASS — `Helpers::e()` escapes output. Script tag rendered as text. |

---

### TC-SEC-03: Password Stored as Hash

| Field | Detail |
|---|---|
| **Test ID** | TC-SEC-03 |
| **Feature** | Credential security |
| **Test Steps** | Register a new account. Check `users` table in database. |
| **Expected Result** | `password` column contains bcrypt hash (starts with `$2y$`), not plaintext. |
| **Actual Result** | ✅ PASS — Passwords stored as bcrypt hashes (cost=12). |

---

### TC-SEC-04: Session Regenerated on Login

| Field | Detail |
|---|---|
| **Test ID** | TC-SEC-04 |
| **Feature** | Session fixation prevention |
| **Test Steps** | Note session ID before login. Login. Check session ID after. |
| **Expected Result** | Session ID changes on successful login (`session_regenerate_id(true)` called). |
| **Actual Result** | ✅ PASS — Session ID changes on login, preventing session fixation attacks. |

---

## 9. Test Cases — Produce Images & Display

### TC-IMG-01: Kontomire Maps to Correct Image

| Field | Detail |
|---|---|
| **Test ID** | TC-IMG-01 |
| **Feature** | `Helpers::produceImage()` — substring-safe mapping |
| **Test Steps** | Call `Helpers::produceImage('Kontomire (Cocoyam Leaves)', 'vegetables')` |
| **Expected Result** | Returns Pexels photo ID 31579980 (taro/cocoyam leaves). NOT the yam photo. |
| **Actual Result** | ✅ PASS — Compound keyword "kontomire" matched before shorter "yam". |

---

### TC-IMG-02: Groundnut Maps to Peanut Image

| Field | Detail |
|---|---|
| **Test ID** | TC-IMG-02 |
| **Feature** | `Helpers::produceImage()` |
| **Test Steps** | Call `Helpers::produceImage('Groundnut', 'legumes')` |
| **Expected Result** | Returns Pexels photo 209371 (peanut pile). |
| **Actual Result** | ✅ PASS |

---

### TC-IMG-03: Unknown Produce Falls Back to Default

| Field | Detail |
|---|---|
| **Test ID** | TC-IMG-03 |
| **Feature** | `Helpers::produceImage()` fallback |
| **Test Steps** | Call `Helpers::produceImage('Locust Beans', 'other')` |
| **Expected Result** | Returns the default Unsplash farm image (yam/harvest). |
| **Actual Result** | ✅ PASS — Default fallback image used. |

---

## 10. Unit Test Results

Unit tests are defined in `tests/HelperTest.php` and `tests/AuthTest.php`.

Run command:
```bash
php tests/run_tests.php
```

Expected output:
```
AgriLink Test Suite
══════════════════════════════════════════════════════════════════
[PASS] Helpers::e() escapes HTML special characters
[PASS] Helpers::e() returns empty string for null
[PASS] Helpers::money() formats with Ghana Cedi symbol
[PASS] Helpers::generateOrderRef() starts with AL-
[PASS] Helpers::generateOrderRef() is 9 characters long
[PASS] Helpers::paginate() returns correct items for page 1
[PASS] Helpers::paginate() returns correct items for page 2
[PASS] Helpers::paginate() handles empty array
[PASS] Helpers::matchScore() gives 40 for matching region
[PASS] Helpers::matchScore() gives 30 for matching category
[PASS] Helpers::matchScore() gives 0 for no match
[PASS] Helpers::statusBadge() contains correct label for 'pending'
[PASS] Helpers::statusBadge() contains correct label for 'delivered'
[PASS] Helpers::statusBadge() handles unknown status gracefully
[PASS] Helpers::sanitize() strips HTML tags
[PASS] Helpers::sanitize() trims whitespace
[PASS] produceImage() maps 'kontomire' to taro/cocoyam Pexels ID
[PASS] produceImage() maps 'cocoyam' before 'yam' (substring safety)
[PASS] produceImage() maps 'cowpea' before 'pea' (substring safety)
[PASS] produceImage() uses default fallback for unknown produce
══════════════════════════════════════════════════════════════════
Results: 20 passed, 0 failed
```

---

## 11. Test Summary

| Category | Total Tests | Passed | Failed |
|---|---|---|---|
| Authentication | 6 | 6 | 0 |
| Farmer Portal | 5 | 5 | 0 |
| Buyer Portal | 6 | 6 | 0 |
| Transport Portal | 3 | 3 | 0 |
| Admin Panel | 2 | 2 | 0 |
| Security | 4 | 4 | 0 |
| Produce Images | 3 | 3 | 0 |
| Unit Tests | 20 | 20 | 0 |
| **Total** | **49** | **49** | **0** |

---

## 12. Defect Log

| Defect ID | Description | Severity | Status | Resolution |
|---|---|---|---|---|
| BUG-001 | Kontomire image matched 'yam' substring in produce name "Cocoyam" | Medium | **Resolved** | Compound keywords reordered before shorter ones in `$map` array |
| BUG-002 | "Recommended for You" showed duplicate produce types | Medium | **Resolved** | Added `$seen` deduplication array in `BuyerController::marketplace()` |
| BUG-003 | Invisible text: `bg-primary-container` (#b1f0ce) paired with `text-white` | High (UI) | **Resolved** | Replaced with `bg-primary` (#2c694e, dark green) across 4 view files |
| BUG-004 | Pexels image IDs with wrong format returning broken images | Medium | **Resolved** | Verified all CDN URLs against live image availability; replaced broken IDs |

---

## 13. Regression Checklist

After any code change, verify the following minimum set before deployment:

- [ ] Login still works for all four roles
- [ ] Each role still redirected to correct dashboard
- [ ] Farmer can still add and view listings
- [ ] Buyer marketplace loads without errors
- [ ] Images load on marketplace (no broken images)
- [ ] Order placement works end-to-end
- [ ] Notifications appear for relevant events
- [ ] Admin dashboard loads
- [ ] No PHP errors in server log

---

*Document prepared for CS 415 Software Engineering Final Project — AgriLink Ghana Agricultural Marketplace Platform*  
*Team: [Team Name] | Date: April 2026*
