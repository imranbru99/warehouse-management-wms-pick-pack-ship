<p align="center">
  <img src="docs/screenshots/dashboard.svg" alt="Warehouse Management WMS dashboard preview" width="100%"/>
</p>

<h1 align="center">Warehouse Management WMS</h1>

<p align="center"><strong>Bin locations, pick-pack-ship and inventory control</strong></p>

<p align="center">
  <a href="https://github.com/imranbru99/warehouse-management-wms-pick-pack-ship"><img alt="GitHub" src="https://img.shields.io/badge/GitHub-imranbru99%2Fwarehouse-management-wms-pick-pack-ship-0F172A?style=for-the-badge&logo=github"/></a>
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-PHP-FF2D20?style=for-the-badge&logo=laravel&logoColor=white"/>
  <img alt="Author" src="https://img.shields.io/badge/Author-Imran%20Ahmed-14B8A6?style=for-the-badge"/>
</p>

---

## Overview

**Warehouse Management WMS** is a complete business application focused on real operational workflows. This README lists the full feature surface so GitHub visitors and search engines can understand every capability of the product.

## Product preview

| Dashboard | Operations |
| :---: | :---: |
| ![Dashboard](docs/screenshots/dashboard.svg) | ![Operations](docs/screenshots/operations.svg) |

## Complete feature list

### Product modules & capabilities

- MASTER PROMPT
- Any attendance machine
- Module Registry + Toggle System
- Build these Filament panels
- Tenant Admin/App panel
- Supplier portal
- Customer / Client portal
- Carrier / Driver portal
- Mobile RF PWA
- Public website
- Core
- Sales orders
- Customer return requests
- Prebuilt report library
- Allocation engine is pluggable
- Build these public pages
- One landing page per module
- API & Developers
- Documentation / Help Center
- Blog with categories/tags
- Public shipment tracking
- Register / Start free trial
- Realistic multilingual data
- Onboarding checklist per tenant
- Accessibility

### Admin / Filament resources

- Asn
- Customer
- Location
- Product
- Purchase Order
- Putaway Task
- Qc Inspection
- Receipt
- Rma Request
- Sales Order
- Shipment
- Stock Adjustment
- Stock Balance
- Stock Ledger Entry
- Supplier
- Warehouse
- Wave

### Domain models

- Allocation
- Asn
- Asn Line
- Belongs To Tenant
- Carton
- Customer
- Load
- Location
- Lpn
- Module Definition
- Pick Task
- Product
- Product Barcode
- Purchase Order
- Purchase Order Line
- Putaway Task
- Qc Inspection
- Receipt
- Receipt Line
- Return Receipt
- Rma Line
- Rma Request
- Sales Order
- Sales Order Line
- Shipment
- Shipment Package
- Stock Adjustment
- Stock Adjustment Line
- Stock Balance
- Stock Ledger Entry
- Supplier
- Tenant
- Tenant Module
- Tracking Event
- Uom
- Warehouse
- Warehouse Task
- Wave
- Zone

### HTTP / API controllers

- Marketing APIs & screens
- Sales Order APIs & screens
- Track Shipment APIs & screens

### Detailed feature requirements delivered

- MASTER PROMPT — Build "Warehouse Management (WMS): bin locations, pick, pack, ship"
- Every phase ends green: migrations run, seeders run, php artisan test passes, pint clean, larastan/phpstan clean at level 6+. Commit after each phase with a clear message.
- Filament for all admin, tenant, and portal panels (multiple panels, see section 4)
- Public website: Blade + Livewire + Tailwind (fast, SEO-friendly, SSR), fully responsive, dark/light mode
- Mobile/RF scanner UI: installable PWA (Livewire + Alpine), works with camera barcode scanning and hardware scanners (keyboard wedge)
- Auth: Laravel Fortify/Breeze-style flows inside Filament, plus Sanctum for the API, 2FA (TOTP), optional social login
- Roles/permissions: spatie/laravel-permission (team-aware per tenant)
- Audit: spatie/laravel-activitylog; media: spatie/laravel-medialibrary; backups: spatie/laravel-backup; settings: spatie/laravel-settings
- Testing: Pest + Livewire testing + Filament testing helpers
- Dev/Ops: Docker Compose (app, mysql, redis, mailpit, horizon, reverb), GitHub Actions CI, .env.example fully documented, make or composer scripts for common tasks
- REQUIRED PACKAGES (use each for its stated purpose)
- Any attendance machine (labor management)
- ARCHITECTURE
- Dependency resolver: enabling a module auto-suggests required modules; disabling one that others depend on is blocked with an explanation.
- Provide Module::enabled('key'), a Blade directive @module('key'), a route middleware module:key, and a Filament trait to hide resources/pages.
- Task engine: unified warehouse task model (receive, putaway, pick, pack, replenish, move, count, load) with assignment, priority, SLA, and state machine.
- PANELS AND USER TYPES
- Tenant Admin/App panel (/app): the main WMS for staff, role-driven navigation.
- Supplier portal (/supplier): view POs, confirm, submit ASN, upload documents, see receiving status and payments.
- Customer / Client portal (/portal): place orders, see stock levels (for 3PL clients, only their own stock), track shipments, request returns, download invoices and documents.
- Carrier / Driver portal (/carrier): appointments, dock schedule, pickups, proof of delivery upload.
- Mobile RF PWA (/rf): scanner-first screens for receiving, putaway, picking, packing, counting, moving, loading.
- MODULES (build ALL; each is toggleable; list in README and on public site)
- Warehouse & Bin Locations (the heart)
- Product & Item Master
- Inbound / Receiving
- Inventory Control
- Outbound: Orders, Allocation, Pick
- Returns / RMA
- Kitting, Assembly & Light Manufacturing
- Cross-Docking & Flow-Through
- Yard & Dock Scheduling
- Labor Management & Attendance
- 3PL / Multi-Client & Billing
- Procurement & Replenishment Planning
- Order & Sales Channel Integrations
- Accounting & ERP Hooks
- Quality & Compliance
- Equipment & Asset Management
- Safety & Incident Management
- Reporting & Analytics
- Automation & Workflow Engine
- Notifications & Communication
- AI Assistant & Intelligence (via laravel-ai-hub, per-tenant opt-in)
- Developer Platform
- API tokens, scopes, webhooks UI, OpenAPI docs, sandbox mode, event log, rate-limit dashboard, SDK examples (curl, PHP, JS, Python) on the public docs site.
- Data Management
- Subscription & Billing (SaaS)
- WAREHOUSE OPERATIONS ENGINE: RULES TO IMPLEMENT CORRECTLY
- Concurrency safety: DB transactions + row locks / Redis locks on allocation and picking; idempotent task completion; no negative stock unless a tenant setting allows it.
- Pick path optimization: serpentine/S-shape by aisle and level using location sequence.
- Cartonization: 3D bin packing heuristic, tested.
- Offline tolerance for RF PWA: queue scans locally and sync (service worker + background sync) with conflict handling.
- PUBLIC WEBSITE (many pages, driven by the module manifests)
- Home: hero, demo CTA, module showcase, industries, stats, testimonials (clearly demo content), integrations strip, pricing teaser, FAQ.
- Features: complete list of every feature of every module (generated from module.json so it always matches README).
- Modules & Presets: "turn what you need on, switch off what you don't" explainer with interactive preset picker.
- Solutions by industry: e-commerce, 3PL, manufacturing, wholesale, retail, food & cold chain, pharma, automotive, fashion, electronics.
- Pricing: plans, module add-ons, monthly/yearly toggle, comparison table, ROI calculator.
- Live Demo page: one-click login cards for every demo user/role (section 9) with a role description of "what you'll see".
- Product tour: interactive walkthrough of receive → putaway → pick → pack → ship.
- Integrations: carriers, marketplaces, accounting, AI providers.
- Public shipment tracking (/track).
- Legal: Terms, Privacy, Cookies, SLA, DPA (generic templates, clearly marked as templates).
- Register / Start free trial (onboarding wizard: business type → preset → warehouse setup → import sample data), Login, Forgot password.
- README.md (generate and keep in sync)
- DEMO DATA AND DEMO USERS
- Provide php artisan wms:demo (and DatabaseSeeder in non-production) that creates a fully populated demo:
- Realistic multilingual data (English, Bangla, Arabic, etc.) to show unicode PDFs and slugs.
- Demo mode: scheduled auto-reset of demo tenants (configurable), banner "You're in demo mode", destructive actions limited, outbound emails/SMS/payments sandboxed.

### Platform capabilities

- Multi-role ready structure for operators, managers and admins
- Database migrations and seeders for demo-ready local setup
- Laravel architecture with services/controllers/models separation
- Responsive UI foundation (Blade / Filament where enabled)
- Environment-based config for local and production databases
- GitHub-ready documentation with visual product previews

## Tech stack

- PHP 8.2+ / Laravel
- Filament admin resources (where enabled)
- Vite asset pipeline
- SQLite/MySQL-ready database layer
- Migrations, seeders, and test-ready structure

## Quick start

```bash
git clone https://github.com/imranbru99/warehouse-management-wms-pick-pack-ship.git
cd warehouse-management-wms-pick-pack-ship
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

## Repository

- GitHub: https://github.com/imranbru99/warehouse-management-wms-pick-pack-ship
- Developer: **Imran Ahmed** · https://imrandev.bd

---

## 🤝 Let's Connect & Collaborate

I am open to **Senior Remote Full-Stack Roles**, AI Platform Architecture Contracts, and Enterprise Technical Advisory.

**Timezone:** UTC+6 (Dhaka / Rangpur, Bangladesh) — flexible overlap with US, UK & EU hours.  
**Delivery:** Async-ready · Slack · Discord · Jira · GitHub · production-first accountability.

| Channel | Handle | Link |
| :--- | :--- | :--- |
| Portfolio | imrandev.bd | https://imrandev.bd |
| Packagist | imrandevbd | https://packagist.org/packages/imrandevbd/ |
| LinkedIn | imranbru99 | https://linkedin.com/in/imranbru99 |
| GitHub | imranbru99 | https://github.com/imranbru99 |
| WhatsApp | +880 1576-918420 | https://wa.me/8801576918420 |
| Email | me@imrandev.bd | mailto:me@imrandev.bd |
| X | @imrandev_bd | https://x.com/imrandev_bd |
| YouTube | @ImranDevBD | https://youtube.com/@ImranDevBD |
