# WMS Architecture & Delivery Plan

**Product:** Warehouse Management (WMS): bin locations, pick, pack, ship  
**Stack:** Laravel 13 · PHP 8.3+ · Filament 5 · MySQL 8 · Redis · Horizon · Reverb · Scout  
**Author:** Imran Ahmed (https://imrandev.bd)

## 1. Architecture overview

```mermaid
flowchart TB
  subgraph Public
    WWW[Blade+Livewire Marketing]
    Track[/track shipment]
    Docs[Help + OpenAPI]
  end
  subgraph Panels
    Platform[/platform Super Admin]
    App[/app Tenant WMS]
    Supplier[/supplier]
    Portal[/portal Client]
    Carrier[/carrier]
    RF[/rf PWA]
  end
  subgraph Core
    Tenancy[tenant_id scopes]
    Modules[Module Registry + Toggle]
    Ledger[StockLedger Service]
    Tasks[Warehouse Task Engine]
    API[/api/v1 REST]
  end
  subgraph Packages
    Drutogoti
    AdminPanel[laravel-admin-panel dashboards]
    AiHub[laravel-ai-hub]
    AiCatalog[laravel-ai-catalog]
    Attendance[laravel-attendance-hub]
    UnicodePdf[laravel-unicode-pdf]
    UniSlug[laravel-universal-slug]
  end
  WWW --> App
  Panels --> Core
  Core --> Packages
```

- **Tenancy:** single DB, `tenant_id` on all tenant rows via `BelongsToTenant` + global scope; subdomain/custom domain resolution middleware.
- **Modules:** `modules/` catalog + `tenant_modules` toggles; `Module::enabled()`, `@module`, `module:` middleware, Filament `HidesWhenModuleDisabled`.
- **Inventory:** immutable `stock_ledger_entries`; on-hand via cached balances reconcilable to ledger. No direct qty edits.
- **Tasks:** unified `warehouse_tasks` state machine (receive, putaway, pick, pack, replenish, move, count, load).
- **Panels:** Filament shell; KPI dashboards embed `laravel-admin-panel` widgets.

## 2. Version pins (Packagist-checked 2026-11-07)

| Package | Pin | Notes |
|---------|-----|-------|
| laravel/framework | ^13.17 | Latest stable |
| filament/filament | ^5.11 | illuminate ^11–13 |
| imrandevbd/laravel-ai-hub | ^1.7.3 | L10–13 |
| imrandevbd/laravel-ai-catalog | dev-main | Requires ai-hub ^1.6 |
| imrandevbd/laravel-unicode-pdf | dev-main | L10–13 |
| imrandevbd/laravel-universal-slug | dev-main | L10–13 |
| imrandevbd/drutogoti | path `packages/drutogoti` | Upstream ^11\|^12 only → bump to ^13 |
| imrandevbd/laravel-admin-panel | path `packages/laravel-admin-panel` | composer.json lags README L13 claim |
| imrandevbd/laravel-attendance-hub | path `packages/laravel-attendance-hub` | Upstream ^10–12 → bump to ^13 |

## 3. Module list (all toggleable except Core)

| Key | Name | Depends on |
|-----|------|------------|
| core | Core Platform | — (always on) |
| warehouse | Warehouse & Bin Locations | core |
| products | Product & Item Master | core |
| inbound | Inbound / Receiving | warehouse, products |
| inventory | Inventory Control | warehouse, products |
| outbound | Orders, Allocation, Pick | inventory, products |
| pack_ship | Pack & Ship | outbound |
| returns | Returns / RMA | pack_ship |
| kitting | Kitting & Light Mfg | inventory, products |
| crossdock | Cross-Docking | inbound, outbound |
| yard | Yard & Dock Scheduling | inbound |
| labor | Labor & Attendance | core |
| billing_3pl | 3PL Billing | inventory |
| procurement | Procurement Planning | inbound, products |
| channels | Order Channel Integrations | outbound |
| accounting | Accounting & ERP Hooks | inventory |
| quality | Quality & Compliance | inventory |
| equipment | Equipment & Assets | core |
| safety | Safety & Incidents | core |
| reporting | Reporting & Analytics | core |
| automation | Automation & Workflow | core |
| notifications | Notifications | core |
| ai | AI Assistant | products |
| developer | Developer Platform | core |
| data_mgmt | Data Management | core |
| saas_billing | Subscription & Billing | core |

## 4. DB schema overview (core + cross-cutting)

- `tenants`, `tenant_domains`, `plans`, `subscriptions`, `modules`, `tenant_modules`
- `users`, `roles`, `permissions` (Spatie teams = tenant_id)
- `warehouses`, `zones`, `aisles`, `racks`, `levels`, `locations`, `lpns`
- `products`, `product_variants`, `barcodes`, `uoms`, `uom_conversions`, `lots`, `serials`
- `suppliers`, `customers`, `addresses`
- `purchase_orders`, `asns`, `receipts`, `putaway_tasks`, `qc_inspections`
- `stock_balances`, `stock_ledger_entries`, `adjustments`, `transfers`, `cycle_counts`
- `sales_orders`, `allocations`, `waves`, `warehouse_tasks`, `shipments`, `cartons`, `loads`
- `returns`, `boms`, `work_orders`, `dock_appointments`
- `employees`, `shifts`, `attendance_*` (hub), `rate_cards`, `client_invoices`
- `webhooks`, `api_tokens`, `automation_rules`, `ai_*`, `reports`

## 5. Phase order

1. **Foundation** — Laravel, Docker, packages, tenancy, auth, modules, panels, i18n, CI  
2. **Master data** — warehouses/locations/map, products, UoM, suppliers, customers  
3. **Inbound** — PO, ASN, receive, QC, LPN, putaway, PDFs  
4. **Inventory** — ledger, adjusts, moves, transfers, counts, replenishment  
5. **Outbound** — orders, allocate, wave, pick, RF, pack, ship, track  
6. **Extended ops** — returns, kitting, cross-dock, yard, quality, equipment, safety  
7. **Labor / 3PL / procurement**  
8. **Portals** — supplier, customer, carrier  
9. **Integrations** — channels, accounting, API, webhooks, automation  
10. **AI** — ai-hub + ai-catalog  
11. **Reporting** — admin-panel dashboards + reports  
12. **SaaS billing** + platform admin  
13. **Public website** + README generator  
14. **Demo data**, performance (drutogoti), security, acceptance  

## 6. Working conventions

- Complete files only; no TODOs/placeholders in shipped UI.
- Commit after each green phase.
- Update `docs/PROGRESS.md` after each phase.
- Package APIs only as documented in `docs/PACKAGES.md`.
