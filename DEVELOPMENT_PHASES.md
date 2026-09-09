# Dynamic QR Social Profile SaaS - Development Roadmap

This document outlines the advanced, enterprise-grade development phases for the SaaS platform, ensuring high availability, scalability, and premium features.

## Phase 1 — Foundation & Core Infrastructure
* **Framework:** Laravel 11 (PHP 8.2+)
* **Database:** MySQL 8.0 / PostgreSQL (Transitioned from SQLite for production)
* **Caching & Queues:** Redis (Horizon for queue monitoring)
* **Authentication:** Laravel Breeze/Fortify with 2FA & Email Verification
* **Authorization:** Spatie standard Role-Based Access Control (User vs. Admin)
* **Frontend:** Tailwind CSS, Alpine.js, Vite

## Phase 2 — Dynamic QR Profiles
* **Profile Engine:** Highly optimized /p/{slug} dynamic routing.
* **Reactive UI:** Alpine.js powered profile builder (live preview).
* **Connections:** Polymorphic relationships for Infinite Custom Links & fixed Social Links.
* **SEO & Meta:** Dynamic OpenGraph tags and customizable SEO titles/descriptions per profile.

## Phase 3 — Enterprise QR Generation
* **Engine:** endroid/qr-code with custom matrix builders.
* **Formats:** High-resolution PNG and scalable SVG exports.
* **Branding:** Center-embedded logos with automatic density scaling.
* **Customization:** Dynamic foreground/background hex colors, block styles (Square/Round).
* **Garbage Collection:** Automated disk cleanup for regenerated QR codes to save S3 storage.

## Phase 4 — Smart Contact Cards (vCard)
* **vCard 4.0 Generation:** Dynamic .vcf file generation injecting custom links and social profiles directly into phone address books.
* **Deep Linking:** Automatic app-opening intent links (	el:, mailto:, wa.me/).
* **Cross-Platform Compatibility:** Tested parsing for both iOS Contacts and Android Google Contacts.

## Phase 5 — High-Frequency Analytics
* **Asynchronous Tracking:** Queue-based event logging (ClickEvent, ScanEvent) to prevent bottlenecking the main profile load speed.
* **Data Points:** Capturing IP Hash (GDPR compliant), Country, Region, Device Type, Browser, and Referrer.
* **Dashboards:** Time-series data visualization using Chart.js/ApexCharts for daily, weekly, and monthly tracking.
* **Automated Reports:** CRON-based weekly and monthly analytics summaries sent via Mailables.

## Phase 6 — Theme & Template Engine
* **Template System:** Modular Blade components for different layout structures (e.g., Minimal, Corporate, Link-in-bio).
* **Theme Customization:** Data-driven CSS variables stored in JSON (	heme_data) allowing users to tweak button radii, fonts, and accents.
* **Premium Gating:** Middleware checks to restrict premium templates to active subscribers only.

## Phase 7 — Global Payments & Subscriptions
* **Billing Engine:** Laravel Cashier for subscription lifecycle management.
* **Gateways:** Dual-gateway approach (Stripe for Global, Razorpay for APAC/India).
* **Webhooks:** Cryptographically verified webhook endpoints to handle asynchronous payments, renewals, and cancellations securely.
* **Invoicing:** Automated PDF invoice generation and billing history dashboards.

## Phase 8 — Centralized Admin Command Center
* **Data Grids:** Advanced DataTables for managing thousands of Users, Profiles, and Plans.
* **Moderation:** Tools to suspend abusive profiles or block malicious domains/IPs.
* **Financial Overview:** Revenue charts, active MRR (Monthly Recurring Revenue), and payment dispute logs.
* **Template Management:** Admin CRUD interface to deploy new templates globally without deploying code.

## Phase 9 — Advanced Enterprise Features
* **Custom Domains (CNAME):** Cloudflare API integration allowing users to map their own domains (e.g., qr.theircompany.com) to their profiles with automated SSL provisioning.
* **Bulk QR Generation:** Laravel Job Batches to allow enterprise users to upload a CSV and generate 10,000+ localized QR codes concurrently in the background.
* **Developer API:** Laravel Sanctum powered REST API with rate limiting, allowing external CRM tools to programmatically create/update profiles.
* **Webhooks:** Outbound webhooks allowing users to receive pingbacks when their profile is scanned.
* **Teams & Multi-Tenancy:** Shared workspaces allowing marketing agencies to manage profiles for multiple clients under one billing account.
* **White Labeling:** Removing all SaaS branding from the dashboard and public profiles for top-tier enterprise clients.
