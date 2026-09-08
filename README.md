# Dynamic QR Social Profile SaaS

A production-ready, secure enterprise SaaS platform built with PHP & Laravel where users create dynamic digital identity profiles containing social media links, custom CTA buttons, and downloadable vCard (`.vcf`) contact cards, each linked to a dynamic vector QR code.

---

## 🏗️ Core Architecture & Principles

### 1. Primary Dynamic QR Guarantee
```text
USER SCANS QR
     ↓
GET /p/{slug}
     ↓
Dynamic Profile Landing Page
     ↓
Website + Social Networks + Custom Buttons + VCF Contact Download
```
- **Permanent URL**: The QR code encodes strictly `https://yourdomain.com/p/{slug}`.
- **Dynamic Updates**: Updating social media links, phone numbers, website URLs, or custom buttons **NEVER requires re-printing or regenerating the QR code**.

### 2. High-Traffic & Asynchronous Queue Subsystem
```text
Web Request (under 50ms)
     ↓
Dispatch TrackAnalytics / ProcessBulkQR / SendEmail
     ↓
Queue Workers (Database / Redis)
     ↓
Asynchronous Processing & Privacy-Safe SHA-256 Analytics
```

### 3. Payment & Webhook Verification Flow
```text
Stripe / Razorpay Webhook
     ↓
Signature Verification Middleware
     ↓
Idempotent Webhook Handler (Event Deduplication)
     ↓
Update Subscription State & Feature Permissions
```

### 4. Scheduler & Maintenance Architecture
```text
Laravel Scheduler (routes/console.php)
     ├── clean:temp-files (Daily temporary QR & export cleanup)
     ├── analytics:aggregate (Hourly scan & click aggregation)
     ├── reports:generate (Daily PDF/CSV analytics reports)
     ├── subscriptions:check (Daily expiry & grace period checks)
     ├── data:cleanup (Weekly old audit log archiving)
     └── emails:send-scheduled (Hourly queue digest emails)
```

---

## 🚀 Installation & Local Setup

### Requirements
- **PHP**: 8.2 or higher
- **Extensions**: `ext-gd` or `ext-imagick`, `ext-pdo_mysql`, `ext-mbstring`, `ext-openssl`, `ext-curl`
- **Database**: MySQL 8.0+ / MariaDB 10.5+ or SQLite
- **Node.js**: 18+ & npm

### Setup Steps
1. **Clone Repository**:
   ```bash
   git clone https://github.com/vermapower564/QR-code.git
   cd QR-code
   ```

2. **Install PHP & Node Dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database & Storage Setup**:
   ```bash
   php artisan migrate:fresh --seed
   php artisan storage:link
   ```

5. **Run Application**:
   ```bash
   php artisan serve
   ```
   Or run the standalone Node preview server:
   ```bash
   node server.js
   ```
   Open `http://localhost:8000`.

---

## 🔑 Default Test Accounts

| Account Role | Email Address | Default Password | Permissions |
|---|---|---|---|
| **System Admin** | `admin@qrsocialsaas.com` | `password` | Full access to `/admin` dashboard, user suspension, plan pricing, audit logs & domain blocklist |
| **Pro User** | `john@example.com` | `password` | Access to `/dashboard`, custom domains, multi-profile management & analytics |

---

## 🧪 Automated Testing Suite

Run the full PHPUnit test suite:
```bash
php artisan test
```
Or run specific test suites:
```bash
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
php artisan test tests/Security/SecurityTest.php
```

### Key Test Coverage
- **Unit Tests**: QR content validation, slug generator & reserved slugs, plan limit gating, SHA-256 IP anonymization, RFC 6350 vCard formatting.
- **Feature Tests**: Registration, login, profile lifecycle, QR multi-format download (PNG, SVG, PDF), link click tracking, Stripe/Razorpay webhook idempotency, admin role authorization.
- **Security Tests**: XSS sanitization, unsafe scheme rejection (`javascript:`, `data:`, `file:`), IDOR defense, **CSV formula injection defense** (`=`, `+`, `-`, `@`), and host domain matching.

---

## 🔒 Security & Anti-Abuse Policies

- **Server-Side Validation**: Untrusted input is strictly validated on the server side using `LinkValidationService`.
- **Dangerous Scheme Rejection**: Rejects `javascript:`, `data:`, `file:`, `vbscript:` URLs.
- **Host Domain Matching**: Prevents host spoofing on platform links (`https://instagram.com.malicious.com`).
- **CSV Formula Injection Defense**: Prepends `'` to cells starting with `=`, `+`, `-`, `@`, `\t`, `\r` during CSV exports.
- **IP Anonymization**: Scans and clicks hash IP addresses with a secret application salt (`hash('sha256', $ip . $salt)`).
- **Audit Logging**: All security-critical actions are server-logged via `AuditLogService` without recording raw passwords, tokens, or payment secrets.

---

## 📦 Disaster Recovery & Backup Procedure

### Automated Backup Command
Run database and asset backup:
```bash
php artisan db:backup
```

### Disaster Recovery Flow
```text
Database Failure / Corruption
      ↓
Restore Latest Verified SQL Dump
      ↓
Verify Storage Assets & Symlinks (`php artisan storage:link`)
      ↓
Verify Dynamic QR Profiles & URL Routes (`/p/{slug}`)
      ↓
Restart Queue Workers (`php artisan queue:restart`)
      ↓
Return Application to Production Status
```

---

## 🚀 Production Deployment Checklist

1. **Environment Setup**: Set `APP_ENV=production` and `APP_DEBUG=false`.
2. **Database Migrations**: Run `php artisan migrate --force` (NEVER run `migrate:fresh` or `db:wipe` in production).
3. **Cache Optimization**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
4. **Queue Worker & Scheduler Setup**:
   - Supervised Queue Worker: `php artisan queue:work --sleep=3 --tries=3`
   - Cron Scheduler: `* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1`
5. **Worker Restart**: Run `php artisan queue:restart` after every deployment release.

---

## 📄 License & Attribution

Designed and engineered following enterprise Laravel best practices and modern security standards.
