# Required Package Research Notes

Researched from Packagist + GitHub README/source on 2026-11-07. Only documented public APIs are used.

## 1. imrandevbd/drutogoti (`Drutogoti\`)

- **Repo:** https://github.com/imranbru99/laravel-speed-optimization-drutogoti  
- **Version:** `dev-main` (path-vendored; illuminate bumped `^11\|^12` → `^11\|^12\|^13`)  
- **Install:** `composer require` → publish `drutogoti-config`, `drutogoti-migrations` → migrate  
- **Env:** `DRUTOGOTI_ENABLED`, `DRUTOGOTI_STORAGE_DRIVER=queue|sync`, slow/N+1 thresholds, sample rate  
- **What it does:** N+1 detection, slow queries, cache hit/miss, cookie bloat, image scan → suggestions tables  
- **App usage:** Enable in non-prod + sampled prod; Performance Filament page reads suggestion/request models; benchmark in `docs/PERFORMANCE.md`

## 2. imrandevbd/laravel-admin-panel (`ImranDevBD\LaravelAdminPanel\`)

- **Repo:** https://github.com/imranbru99/laravel-admin-panel  
- **Version:** `dev-main` (path-vendored for L13)  
- **API:** `AdminPanel` facade, `Panel`/`PanelManager`, `NavigationManager`, dashboard Blade views, widgets/KPI components, `admin-panel:install`  
- **Integration rule:** Filament is the shell; embed Admin Panel dashboard widgets/views inside Filament dashboard pages (do not run a competing full admin UI).

## 3. imrandevbd/laravel-ai-hub (`ImranDevBd\AiHub\`) — v1.7.3

- **Install:** publish `ai-hub-config`, migrate  
- **API:** `AIHub::prompt()`, `AIHub::model()`, `AIHub::chat()`, `AIHub::fake()`, `AIHub::structured()`, streaming SSE, Studio `/ai-hub`  
- **Filament:** `AiHubPlugin`, `AiHubDashboardWidget`  
- **App usage:** All AI features; per-tenant provider/keys (encrypted); fake provider in demo/tests; usage metering via request logs

## 4. imrandevbd/laravel-ai-catalog (`ImranDevBd\AiCatalog\`)

- **Depends on:** `laravel-ai-hub ^1.6`  
- **API:** `AiCatalog::fromKeyword|fromUrl|fromImage|...()->generate()`, `AiCatalog::approve($draft)`, Filament `GenerateWithAiAction`, Studio `/ai-catalog`  
- **App usage:** Product import wizard “Fill from URL/web”, bulk enrichment, supplier price list scrape via `UrlReader`

## 5. imrandevbd/laravel-attendance-hub (`ImranDevBd\AttendanceHub\`)

- **Version:** path-vendored for L13  
- **API:** `AttendanceHub` facade, drivers (`FakeDeviceDriver`, `VirtualDriver`, ZKTeco, Hikvision, …), `attendance-hub:simulate`, `attendance-hub:sync`, Filament plugin  
- **Models:** devices, logs, user maps, biometric templates; `AttendanceCalculator` for late/OT  
- **App usage:** Labor module device registry + productivity linkage to pick/pack tasks; demo uses Virtual/Fake driver

## 6. imrandevbd/laravel-unicode-pdf (`ImranDev\UnicodePdf\`)

- **API:** `UnicodePdf::preset('bengali'|'arabic'|...)->loadView(...)->a4()->download|stream|save()`  
- **Engines:** native (default), optional Dompdf/mPDF/TCPDF/Browsershot  
- **Fonts:** `php artisan unicode-pdf:font:install --font=bengali|arabic|universal`  
- **App usage:** ALL PDFs (pick lists, packing slips, invoices, labels, manifests, BOLs, POs, RMAs, reports)

## 7. imrandevbd/laravel-universal-slug (`ImranDev\UniversalSlug\`)

- **API:** `universal_slug()`, `UniversalSlug::of()`, `HasUniversalSlug` trait, `#[Slug(from: 'title')]`, `uniqueScope()` for tenant  
- **Modes:** `preserve_unicode` (default), `transliterate`, `ascii_only`  
- **App usage:** products, categories, brands, warehouses, public pages, blog, tenant subdomains

## Spatie / Laravel first-party (also used)

| Package | Purpose |
|---------|---------|
| spatie/laravel-permission | Team-aware roles (tenant) |
| spatie/laravel-activitylog | Audit trail |
| spatie/laravel-medialibrary | Media |
| spatie/laravel-backup | Backups |
| spatie/laravel-settings | Settings |
| laravel/horizon | Queues |
| laravel/reverb | Realtime |
| laravel/scout | Search (database driver default) |
| laravel/sanctum | API tokens |
| pestphp/pest | Tests |
| larastan/larastan | PHPStan level 6+ |
