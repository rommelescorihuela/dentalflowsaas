# AGENTS.md — DentalFlow SaaS

## Quick Facts
- **Stack**: Laravel 12, Filament 4.x, Livewire 3.x, Tailwind 4, PostgreSQL 14+
- **Multi-tenancy**: Stancl Tenancy 3.9 (tenant ID = clinic subdomain)
- **Auth/RBAC**: Spatie Permissions 6.0
- **Payments**: Laravel Cashier (Stripe)
- **Tests**: ~182 tests across 22 Feature + 1 Unit file

## Critical Commands
```bash
# Dev server (server + queue + logs + vite)
composer run dev

# Run all tests (runs config:clear first)
composer run test
# or: php artisan test

# Single test
php artisan test --filter=SecurityTenantIsolationTest

# Full system diagnostic
php artisan diagnostic:all
php artisan diagnostic:all --skip-tests

# Setup from scratch
composer run setup
```

## Database
- **PostgreSQL only** — `.env.example` defaults to `sqlite`, **must** override to `pgsql`
- **Test DB**: `dentalflow_test` on `127.0.0.1:5432` (hardcoded in `phpunit.xml`)
- Test credentials in `.env.testing` (gitignored)
- `phpunit.xml` env vars match local PostgreSQL — adjust if different

## Tenant Identification
Three approaches depending on route group:

| Route Group | Middleware | How tenant is identified |
|---|---|---|
| **Filament / App** | `InitializeTenancyBySubdomainId` | Subdomain of host (e.g. `clinic1.dentalflow.dev` → tenant `clinic1`) |
| **Portal** (patient) | `InitializeTenancyByPath` | URL path segment: `/{tenant}/portal/{patient}` |
| **Central** (admin) | None | No tenant — central domain only |

The custom `InitializeTenancyBySubdomainId` middleware splits the host on `.` and uses the first segment as the tenant ID. Central domains (`localhost`, `127.*`) are skipped.

## Access URLs on Local Dev
```
Central admin:  http://127.0.0.1:8000/admin
Clinic panel:   http://127.0.0.1:8000/{tenant}/app       (e.g. /clinic1/app)
Patient portal: http://127.0.0.1:8000/{tenant}/portal/{patient}
```

Existing tenants: `clinic1`, `clinic2`.

## Middleware Chain (Clinic Panel)
```
InitializeTenancyBySubdomainId → SetTenancyUrlDefaults → SyncSpatiePermissionsTeamId
```

- `SyncSpatiePermissionsTeamId` ties Spatie permissions to the current clinic ID
- `SetTenancyUrlDefaults` configures Filament tenant-aware URLs
- Portal routes also use `signed` URLs and `throttle:portal` (30 req/min per IP)

## Tenant Isolation
- Models use `BelongsToClinic` trait → `ClinicScope` global scope filtering by `clinic_id`
- `clinic_id` is auto-set on `creating` from `tenant()->getTenantKey()`
- Super admin bypasses tenant scopes
- 18 models: `Patient`, `Odontogram`, `ClinicalRecord`, `Budget`, `BudgetItem`, `Appointment`, `Treatment`, `Payment`, `ProcedurePrice`, `Inventory`, `ProcedureInventory`, `Clinic`, `Domain`, `User`, `Role`, `Permission`, `SystemActivity`, `SubscriptionPayment`

## Key Directories
```
app/Filament/App/Resources/     — 9 clinic-panel resources (Patients, Budgets, Appointments, etc.)
app/Filament/App/Widgets/       — CalendarWidget, StatsOverview, RevenueChart, etc.
app/Filament/App/Pages/         — ClinicSettings
app/Filament/Resources/         — 5 shared central resources (Clinics, Roles, Users, etc.)
app/Livewire/                   — Odontogram (interactive SVG), Auth/RegisterTenant, PatientPortal/BookAppointment
app/Models/                     — 18 Eloquent models
app/Services/                   — BudgetGenerator, TenantService
app/Traits/                     — BelongsToClinic, HasSpatiePermissions, ActivityLogger
app/Console/Commands/           — SystemDiagnosticCommand, TestRoutesCommand, CheckSchema, DebugTenant
```

## Build & Assets
- **Vite** with `@tailwindcss/vite` plugin (Tailwind 4 CSS)
- Entry points: `resources/css/app.css`, `resources/js/app.js`
- Run `npm run dev` for HMR during development

## Testing Notes
- Requires PostgreSQL `dentalflow_test` database to exist before running
- Uses `RefreshDatabase` + `$this->switchTenant('clinic-a')`
- Portal routes require `signed` URLs in tests
- CI (`.github/workflows/ci.yml`) runs: `composer install` → `npm ci` → `npm run build` → `php artisan migrate --force` → `php artisan test`
- CI also runs `phpstan analyse app --level=5` (no `phpstan.neon` — uses defaults)
- Notable test files: `SecurityTenantIsolationTest` (9 tests), `OdontogramFunctionalTest` (10), `HttpApiTest` (20), `BudgetGeneratorTest` (8), `CalendarWidgetValidationTest` (6)

## Odontogram
- SVG-based interactive component with 32 teeth, 6 surfaces each
- Procedures read dynamically from `procedure_prices` CRUD (not hardcoded)
- `clinical_records` stores `procedure_price_id` for direct procedure linkage
- `diagnosis_code` is resolved from procedure for color mapping (40+ colors)
- Changing status to `completed` auto-generates a draft budget via `BudgetGenerator`
- `OdontogramObserver` triggers budget generation + toast notification

## Setup
```bash
composer install
cp .env.example .env     # then edit .env: DB_CONNECTION=pgsql + credentials
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install
npm run build
php artisan make:filament-user   # create admin user
```

## Production Notes
- `DEPLOY.md` covers manual, Docker Compose, and Forge/Vapor deployment
- Dockerfile: PHP 8.3-fpm with `--no-dev`
- CI/CD: `.github/workflows/ci.yml` (tests, phpstan, security audit via `composer audit`)
- Transactional emails: budget sent, appointment reminders, password reset
- Legal: `/terms`, `/privacy` routes
