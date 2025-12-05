# Admin Panel Implementation Guide

## Overview

This document covers the complete Admin Panel feature implementation for the DPFines application, including secure authentication, authorization, CRUD operations for fines, and a workflow for reviewing and approving scraped fine data.

## Security Architecture

### Authentication
- **Session-based**: Uses Laravel's built-in session authentication
- **Password hashing**: All passwords are hashed using bcrypt
- **CSRF protection**: All POST/PUT/DELETE requests include CSRF tokens
- **Email validation**: Email format and uniqueness validation on registration

### Authorization
- **Role-based access control**: Users have `is_admin` flag
- **Middleware protection**: `EnsureUserIsAdmin` middleware guards all admin routes
- **Policy-based authorization**: Fine-grained policies for GlobalFine and ScrapedFine models
- **Last login tracking**: Admin logins update `last_login_at` timestamp for audit trails

### Input Validation
- **Form requests**: Custom `StoreGlobalFineRequest` and `ReviewScrapedFineRequest` classes
- **Server-side validation**: All inputs validated before database operations
- **Fine amount constraints**: Positive numbers only, max 999,999,999,999.99
- **Date validation**: Past dates only, prevents future-dated fines
- **URL validation**: Case links must be valid URLs
- **Summary length**: Required 10-5000 characters to prevent empty/spam submissions

## Database Schema

### Users Table Changes
```php
- is_admin (boolean, default: false)
- last_login_at (timestamp, nullable)
```

### Scraped Fines Table
```php
- id (primary key)
- organisation, regulator, sector, region (string)
- fine_amount (decimal), currency (string)
- fine_date (date), law, articles_breached, violation_type (string)
- summary (longtext), badges, link_to_case (string)
- status (enum: pending/approved/rejected)
- reviewed_by (foreign key → users.id)
- reviewed_at (timestamp)
- review_notes (text)
- submitted_by (foreign key → users.id)
- timestamps, soft_deletes
```

## Directory Structure

```
app/
├── Http/
│   ├── Controllers/Admin/
│   │   ├── AuthController.php         # Login/Register/Logout
│   │   ├── DashboardController.php    # Admin dashboard stats
│   │   ├── GlobalFineController.php   # Fine CRUD
│   │   └── ScrapedFineController.php  # Scrap review workflow
│   ├── Middleware/
│   │   └── EnsureUserIsAdmin.php      # Admin gate middleware
│   └── Requests/Admin/
│       ├── StoreGlobalFineRequest.php
│       ├── StoreScrapedFineRequest.php
│       └── ReviewScrapedFineRequest.php
├── Models/
│   ├── User.php                       # Updated with is_admin, relationships
│   ├── GlobalFine.php                 # Existing fine model
│   └── ScrapedFine.php               # New scraped fine model
├── Policies/
│   ├── GlobalFinePolicy.php          # Fine CRUD authorization
│   └── ScrapedFinePolicy.php         # Scrap review authorization
└── Providers/
    └── AppServiceProvider.php         # Policy registration & gates

database/
├── migrations/
│   ├── 2025_12_02_000001_add_is_admin_to_users_table.php
│   └── 2025_12_02_000002_create_scraped_fines_table.php
└── factories/
    ├── GlobalFineFactory.php
    └── ScrapedFineFactory.php

resources/views/admin/
├── auth/
│   ├── login.blade.php
│   └── register.blade.php
├── dashboard.blade.php
├── fines/
│   ├── create.blade.php
│   ├── edit.blade.php
│   ├── index.blade.php
│   └── show.blade.php
└── scraped_fines/
    ├── create.blade.php
    ├── index.blade.php
    ├── review.blade.php
    └── show.blade.php

tests/Feature/Admin/
├── AdminAuthenticationTest.php
├── AdminFinesTest.php
└── ScrapedFineReviewTest.php
```

## Admin Features

### 1. Authentication
- **Login** (`/admin/login`): Email & password authentication
- **Register** (`/admin/register`): Create first admin account (subsequent registrations require existing admin)
- **Logout** (`/admin/logout`): Session termination with CSRF protection

**Security**:
- Non-admin users cannot login to admin panel
- Password field hidden with asterisks
- Session regeneration on login/logout
- CSRF tokens required on all state-changing requests

### 2. Dashboard
- **URL**: `/admin/dashboard`
- **Stats**: Total fines, pending reviews, approved, rejected counts
- **Recent activity**: Latest scraped fines & pending reviews
- **Quick actions**: Links to CRUD operations

### 3. Global Fines Management
- **List** (`/admin/fines`): Paginated list with search & filters
- **Create** (`/admin/fines/create`): Add new fine to database
- **View** (`/admin/fines/{id}`): Detailed fine information
- **Edit** (`/admin/fines/{id}/edit`): Update fine details
- **Delete** (`/admin/fines/{id}`): Soft delete fine record

**Validation Rules**:
- Organisation: Required, 3+ characters
- Fine amount: Required, positive, ≤999,999,999,999.99
- Currency: Required (EUR, USD, GBP, AUD, CAD)
- Fine date: Required, past or today only
- Summary: Required, 10-5000 characters
- Link: Optional, must be valid URL if provided

### 4. Scraped Fines Review Workflow

#### Submit Fine (Workflow Entry)
- **URL**: `/admin/scraped-fines/create`
- **Status**: `pending` on submission
- **Submitted by**: Current authenticated admin user
- **Validation**: Same as global fines

#### Review Pending Fines
- **URL**: `/admin/scraped-fines?status=pending`
- **List view**: Shows pending fines with submitter info
- **Status badge**: Visual indicator (pending/approved/rejected)

#### Approve/Reject Review
- **URL**: `/admin/scraped-fines/{id}/review`
- **Decision**: Radio button (Approve/Reject)
- **Notes**: Required (10-2000 chars) - justification for decision
- **Outcomes**:
  - **Approved**: Copied to `global_fines` table, status = approved
  - **Rejected**: Stays in `scraped_fines` only, status = rejected

#### Audit Trail
- `submitted_by`: User who submitted fine
- `reviewed_by`: Admin who reviewed fine
- `reviewed_at`: Timestamp of review
- `review_notes`: Decision justification

## Routes

```php
// Public routes
GET  /
POST /newsletter/subscribe
GET  /newsletter/unsubscribe/{token}

// Admin routes (guest)
GET  /admin/login                           (show login form)
POST /admin/login                           (authenticate)
GET  /admin/register                        (show register form, first admin only)
POST /admin/register                        (create first admin)

// Admin routes (protected)
POST /admin/logout                          (logout with auth middleware)
GET  /admin/dashboard                       (dashboard stats)

// Fine CRUD (protected)
GET    /admin/fines                         (list all fines)
GET    /admin/fines/create                  (show create form)
POST   /admin/fines                         (store new fine)
GET    /admin/fines/{fine}                  (show fine details)
GET    /admin/fines/{fine}/edit             (show edit form)
PUT    /admin/fines/{fine}                  (update fine)
DELETE /admin/fines/{fine}                  (delete fine)

// Scraped fines (protected)
GET    /admin/scraped-fines                 (list with status filter)
GET    /admin/scraped-fines/create          (show submit form)
POST   /admin/scraped-fines                 (submit for review)
GET    /admin/scraped-fines/{id}            (show fine details)
GET    /admin/scraped-fines/{id}/review     (show review form)
POST   /admin/scraped-fines/{id}/review     (submit review decision)
DELETE /admin/scraped-fines/{id}            (delete scraped fine)
```

## Setup Instructions

### 1. Run Migrations
```bash
php artisan migrate
```

This creates the `is_admin` column on users table and new `scraped_fines` table.

### 2. Create First Admin Account
Navigate to `/admin/register` and create the first admin account. After the first admin is created, registration will be disabled for non-authenticated users.

### 3. Login
Navigate to `/admin/login` and enter admin credentials.

### 4. Access Admin Panel
Once logged in, you can access:
- `/admin/dashboard` - Overview
- `/admin/fines` - Manage global fines
- `/admin/scraped-fines` - Review scraped fines

## Security Best Practices Implemented

### Input Protection
- ✅ Server-side validation on all inputs
- ✅ CSRF token protection on forms
- ✅ Email format validation
- ✅ Numeric constraints (fine amounts)
- ✅ Date constraints (past dates only)
- ✅ URL validation for case links
- ✅ String length validation (summary, notes)

### Output Protection
- ✅ Blade template auto-escaping
- ✅ Proper use of `{{}}`  for user content
- ✅ No raw SQL queries (using Eloquent ORM)

### Authentication
- ✅ Bcrypt password hashing
- ✅ Session-based authentication
- ✅ Login attempt validation
- ✅ Non-admin account rejection
- ✅ Session regeneration on login/logout

### Authorization
- ✅ Middleware-based route protection
- ✅ Policy-based action authorization
- ✅ Admin-only fine CRUD
- ✅ Review status validation (can't review reviewed fines)
- ✅ Soft deletes for audit trail preservation

### Audit Trail
- ✅ `submitted_by` tracks fine submitter
- ✅ `reviewed_by` tracks reviewer
- ✅ `reviewed_at` timestamps reviews
- ✅ `review_notes` documents decisions
- ✅ `last_login_at` tracks admin access
- ✅ Soft deletes preserve deleted records

## Testing

Run all admin tests:
```bash
php artisan test tests/Feature/Admin
```

Run specific test suite:
```bash
php artisan test tests/Feature/Admin/AdminAuthenticationTest
php artisan test tests/Feature/Admin/AdminFinesTest
php artisan test tests/Feature/Admin/ScrapedFineReviewTest
```

### Test Coverage

**Authentication Tests** (11 tests)
- Login/logout/registration flows
- Non-admin rejection
- Password validation
- Session management

**Fine CRUD Tests** (9 tests)
- Create/read/update/delete operations
- Field validation (amount, date, organization)
- Non-admin access denial
- Data persistence

**Review Workflow Tests** (10 tests)
- Submit scraped fine
- Approve/reject decisions
- Data copying on approval
- Review prevention on reviewed fines
- Audit trail recording

## Future Enhancements

- Two-factor authentication (2FA)
- Role-based access (reviewer vs. admin)
- Bulk import CSV of scraped fines
- Email notifications on review decisions
- Fine history/change log
- Advanced search with date ranges
- Export fines to CSV/PDF
- Rate limiting on submissions
