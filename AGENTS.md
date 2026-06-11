# AGENTS.md — DentalFlow SaaS

## Stack
- **Laravel 12**, **Filament 4.x**, **Livewire 3.x**, **Tailwind 4** (via `@tailwindcss/vite` plugin — no config file)
- **PostgreSQL** only (≥14); `.env.example` defaults to `sqlite` — **must override**
- **Multi-tenancy**: Stancl Tenancy 3.9 with shared-DB approach (`clinic_id` column on every tenant model)
- **Auth/RBAC**: Spatie Permissions 6.0 (no Filament Shield — custom role management)
- **Payments**: Laravel Cashier (Stripe)
- **Linting**: Laravel Pint (no `pint.json` — Laravel defaults)
- **Static analysis**: PHPStan level 5, no config file (`phpstan analyse app --level=5`)
- **189 tests**: 22 Feature files + 1 Unit stub (tests/Unit/ExampleTest.php)

## Critical Commands

```bash
composer run dev       # server + queue:listen + pail --timeout=0 + vite (concurrently)
composer run test      # config:clear + php artisan test
composer run setup     # composer install + copy .env + key:generate + migrate + npm install + build

php artisan test --filter=SecurityTenantIsolationTest
php artisan diagnostic:all [--skip-tests]
```

## Database

- **PostgreSQL required** — `.env.example` sets `DB_CONNECTION=sqlite`, change to `pgsql`
- **Test DB**: `dentalflow_test` on `127.0.0.1:5432` (hardcoded in `phpunit.xml`; usernames/passwords via `.env.testing` which is gitignored)
- CI uses PostgreSQL **15** service container (not 14)
- Single shared database — no per-tenant databases; tenant scoping is via `clinic_id` column + global scopes

## Tenant Identification

| Route Group | Middleware | How tenant is identified |
|---|---|---|
| **Filament app panel** (`/app`) | `InitializeTenancyBySubdomainId` | First segment of host split on `.` → tenant ID (e.g. `clinic1.dentalflow.dev` → `clinic1`) |
| **Portal** (patient) | `InitializeTenancyByPath` | URL path: `/{tenant}/portal/{patient}` |
| **Central admin** (`/admin`) | None | No tenant — central domain only |

`InitializeTenancyBySubdomainId` skips domains listed in `tenancy.central_domains` and also skips `localhost`/`127.*`. On local dev (`localhost`), tenancy is NOT initialized by URL — the user's `clinic_id` (set after login) scopes the data via global scope. The App panel is accessed at `/app` directly.

`config('tenancy.central_domains')` defaults to `['localhost']` — **must be set for production** (e.g. `dentalflow.digitalwebsolution.info`).

## Access URLs (Local Dev)

```
Central admin:  http://127.0.0.1:8000/admin
Clinic panel:   http://127.0.0.1:8000/app                 (tenant via user clinic_id after login)
Patient portal: http://127.0.0.1:8000/{tenant}/portal/{patient}
```

Existing tenants: `clinic1`, `clinic2`.

## Middleware Chain

### Filament App Panel (`AppPanelProvider`, panel id: `app`)
```
EncryptCookies → ... → InitializeTenancyBySubdomainId → SetTenancyUrlDefaults → SyncSpatiePermissionsTeamId → Authenticate
```

### Admin Panel (`AdminPanelProvider`, panel id: `admin`, default panel)
```
Standard Filament middleware (NO tenancy middleware — it's central)
```

- `SetTenancyUrlDefaults` falls back to `$request->segment(1)` if `tenant()` is not set, skipping known segments like `admin`, `up`, `login`, `register`, `livewire`
- `SyncSpatiePermissionsTeamId` calls `setPermissionsTeamId()` and clears cached permissions — ties Spatie to current clinic
- Portal routes also use `signed` URLs and `throttle:portal` (30 req/min per IP)

## Tenant Isolation

- The tenant-ID column on Stancl is overridden to `clinic_id` in `TenancyServiceProvider::boot()`
- Models use `BelongsToClinic` trait → adds `ClinicScope` global scope filtering by `clinic_id`
- `clinic_id` is auto-set on `creating` from `tenant()->getTenantKey()` when tenancy is initialized
- Use `->withoutTenancy()` or `->withoutGlobalScope(ClinicScope::class)` to bypass (super admin)
- Models with tenant scope: Patient, Odontogram, ClinicalRecord, Budget, BudgetItem, Appointment, Treatment, Payment, ProcedurePrice, Inventory, ProcedureInventory, Clinic, Domain, User, Role, Permission, SystemActivity, SubscriptionPayment

## Test Conventions

- Base class `Tests\TestCase` (NOT `PHPUnit\Framework\TestCase`) provides:
  - `setUpTenants()` — creates `clinic-a` / `clinic-b`, users, roles, permissions, patients
  - `switchTenant($id)` — `Tenancy::initialize($id)`
  - `actingAsDoctor()`, `actingAsAdmin()`, `actingAsAssistant()`, `actingAsSuperAdmin()`
  - Factory helpers: `createOdontogram()`, `createClinicalRecord()`, `createBudget()`, `createBudgetWithItems()`, `createAppointment()`, `createPayment()`, `createProcedurePrice()`, `createInventoryItem()`
- Tests use `RefreshDatabase` (truncates tables, NOT transactions due to PostgreSQL)
- Portal route tests require `signed` URLs
- Call `setUpTenants()` in `setUp()` for tests needing tenants

## Filament Panels

| Panel | ID | Path | Resources | Themes |
|---|---|---|---|---|
| **Admin** (central) | `admin` (default) | `/admin` | `app/Filament/Resources/` | `resources/css/filament/admin/theme.css` |
| **App** (tenant) | `app` | `/app` | `app/Filament/App/Resources/` | `resources/css/filament/app/theme.css` |

- Both panels use `Instrument Sans` font, dark mode enabled, sidebar collapsible
- Vite entry points (from `vite.config.js`): `resources/css/app.css`, `resources/js/app.js`, `resources/css/filament/app/theme.css`, `resources/css/filament/admin/theme.css`

## Key Directories (non-obvious)

```
app/Filament/App/Resources/     — Clinic-panel resources (auto-discovered)
app/Filament/App/Pages/         — ClinicSettings page
app/Filament/Resources/         — Central admin resources (auto-discovered)
app/Http/Middleware/            — InitializeTenancyBySubdomainId, SetTenancyUrlDefaults, SyncSpatiePermissionsTeamId
app/Livewire/                   — Odontogram (interactive SVG), Auth/RegisterTenant, PatientPortal/BookAppointment
app/Services/BudgetGenerator.php — Auto-generates budgets from completed odontograms
app/Traits/BelongsToClinic.php  — Global scope trait for all tenant models
app/Scopes/ClinicScope.php      — Global scope: filters by clinic_id when tenancy initialized
app/Models/                     — 18 tenant-scoped models
app/Observers/                  — OdontogramObserver (budget generation), AppointmentObserver (inventory deduction)
```

## Odontogram (Complex Logic)

- SVG-based Livewire component, 32 teeth × 6 surfaces each
- Procedures read dynamically from `procedure_prices` CRUD (47+ procedures, not hardcoded)
- `clinical_records.procedure_price_id` links directly to a procedure; `diagnosis_code` is a fallback for color mapping (40+ colors)
- `BudgetGenerator` runs in a DB transaction when odontogram status changes to `completed`:
  1. Checks for existing budget via `odontogram_id` (no duplicates)
  2. Skips `treatment_status = 'completed'` records
  3. Resolves price from `procedure_price_id` first, then falls back to `diagnosis_code` lookup
  4. Groups items by procedure, appends tooth numbers to treatment name
  5. Sets 30-day expiration, `draft` status
- `OdontogramObserver` triggers budget generation + toast notification

## CI/CD (`.github/workflows/ci.yml`)

- **Tests**: `composer install` → `npm ci` → `npm run build` → `cp .env.example .env` → `key:generate` → `php artisan migrate --force` → `php artisan test`
- **Code quality**: PHPStan level 5 on `app/` (non-blocking via `|| true`)
- **Security**: `composer audit` (non-blocking)
- Triggers: push to `main`/`master`/`develop`, PR to `main`/`master`
