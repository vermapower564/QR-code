# Dynamic QR Social Profile SaaS

A SaaS platform built with PHP & Laravel where users can create personal/business digital identity profiles containing social media links, custom CTA buttons, and downloadable vCard (.vcf) contact files, each linked to a dynamic vector QR code.

## Key Features

- **Dynamic QR Codes**: QR codes encode strictly the profile landing URL (`/p/{slug}`), allowing users to update their social links anytime without reprinting their QR code.
- **Multi-Format Export**: High-resolution PNGs (512, 1024, 2048), vector SVG, and printable PDF frames ("SCAN ME").
- **Custom QR Studio**: Custom background/foreground hex colors, module dot styles, and central watermark logos.
- **Dynamic vCard Generation**: Dynamic RFC 6350 compliant `.vcf` file download for instant phone contact saving.
- **Analytics & Tracking**: Real-time scan counts, unique visitors, hashed IP tracking, outbound link click tracking, device/browser/country breakdown with Chart.js charts.
- **SaaS Subscriptions & Feature Gating**: Tiered subscription plans (Free, Pro, Business) with limit checks.
- **Admin Panel**: Global overview metrics, user suspension, plan pricing manager, template manager.
- **REST API & Webhooks**: Laravel Sanctum API endpoints and outbound webhook events.

## Recommended Technology
- **Backend**: PHP 8.2+, Laravel 11/12+
- **Frontend**: Blade, Tailwind CSS, Alpine.js, Chart.js
- **QR Engine**: Endroid QR Code
- **PDF Engine**: Dompdf

## Setup & Running Locally

1. Clone repository and install dependencies:
   ```bash
   git clone https://github.com/vermapower564/QR-code.git
   cd QR-code
   composer install
   npm install
   npm run build
   ```

2. Configure environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. Run migrations and database seeders:
   ```bash
   php artisan migrate:fresh --seed
   php artisan storage:link
   ```

4. Serve the application:
   ```bash
   php artisan serve
   ```
   Open `http://localhost:8000`.

   - Demo User: `john@example.com` / `password`
   - Admin User: `admin@qrsocialsaas.com` / `password`
