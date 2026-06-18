# AGENTS.md — DentalFlow SaaS

## Stack
- **Laravel 12**, **Filament 4.x**, **Livewire 3.x**, **Tailwind 4** (via `@tailwindcss/vite` plugin — no `tailwind.config.js`)
- **PostgreSQL** only (≥14); `.env.example` ships with `DB_CONNECTION=sqlite` — **must override to `pgsql`**
- **Multi-tenancy**: Stancl Tenancy 3.9, shared-DB approach (single DB, `clinic_id` column + global scope — see Tenancy section)
- **Auth/RBAC**: Spatie Permissions 6.0 (no Filament Shield — custom role management; 4 roles: `super-admin`, `admin`, `doctor`, `assistant`)
- **Payments**: Laravel Cashier (Stripe)
- **Linting**: Laravel Pint (no `pint.json` — Laravel defaults)
- **Static analysis**: PHPStan level 5, no `phpstan.neon` (`vendor/bin/phpstan analyse app --level=5`)
- **189 tests**: 22 Feature files + 1 Unit stub (`tests/Unit/ExampleTest.php`)

## Critical Commands

```bash
composer run dev       # server + queue:listen + pail --timeout=0 + vite (concurrently)
composer run test      # config:clear + php artisan test
composer run setup     # composer install + copy .env + key:generate + migrate + npm install + build

php artisan test --filter=SecurityTenantIsolationTest   # single test class
php artisan diagnostic:all [--skip-tests]               # SystemDiagnosticCommand
php artisan test:routes                                 # TestRoutesCommand
php artisan tenants:create                              # Stancl-provided tenant creator
```

## Database & Test Setup

- **PostgreSQL required** — `.env.example` sets `DB_CONNECTION=sqlite`, change to `pgsql`.
- **Test DB**: `dentalflow_test` on `127.0.0.1:5432`, hardcoded in `phpunit.xml`. Credentials (`DB_USERNAME`/`DB_PASSWORD`) are **NOT** set in `phpunit.xml` — provide them locally via `.env.testing` (gitignored); CI injects `postgres`/`postgres` as workflow env.
- CI uses the **postgres:15** service container (not 14).
- **Single shared database** — no per-tenant databases. Tenant isolation is via the `ClinicScope` global scope, not connection switching (see below).

## Tenancy Architecture

**Tenant identification:**

| Route group | Middleware | How tenant is identified |
|---|---|---|
| Filament app panel (`/app`) | `InitializeTenancyBySubdomainId` | First segment of host split on `.` → tenant ID (`clinic1.dentalflow.dev` → `clinic1`) |
| Portal (patient) | `InitializeTenancyByPath` (Stancl) | URL path: `/{tenant}/portal/{patient}` |
| Central admin (`/admin`) | none | No tenant — central domain only |

`InitializeTenancyBySubdomainId` skips hosts in `config('tenancy.central_domains')` and also short-circuits when the first host segment is `localhost` or `127`. **On local dev (`localhost`) tenancy is NOT initialized by URL** — the logged-in user's `clinic_id` scopes data via the global scope. The App panel is accessed at `/app` directly.

`config('tenancy.central_domains')` = `['localhost', '127.0.0.1']` merged with `TENANCY_CENTRAL_DOMAINS` env var (assembled in `AppServiceProvider::boot`). **Must be set for production** (e.g. `dentalflow.digitalwebsolution.info`).

**Tenant isolation:**
- The Stancl tenant-ID column is overridden to `clinic_id` in `TenancyServiceProvider::boot()` (`BelongsToTenant::$tenantIdColumn = 'clinic_id'`).
- `BelongsToClinic` trait (`app/Traits/`) adds the `ClinicScope` global scope and auto-sets `clinic_id` on `creating` from `tenant()->getTenantKey()` when tenancy is initialized.
- `ClinicScope` is a **no-op when `tenancy()->initialized` is false** (so central/admin contexts see unscoped data).
- Bypass with `->withoutTenancy()` (builder macro) or `->withoutGlobalScope(ClinicScope::class)`.
- **12 models use `BelongsToClinic`**: Appointment, Budget, BudgetItem, ClinicalRecord, Inventory, Odontogram, Patient, Payment, ProcedureInventory, ProcedurePrice, Treatment, User. Spatie `Role`/`Permission` are scoped via `setPermissionsTeamId()` instead (not the trait); `Clinic`/`Domain` are the Stancl tenant/domain models; `SystemActivity`/`SubscriptionPayment` are not trait-scoped.
- **Tenancy bootstrappers gotcha**: `DatabaseTenancyBootstrapper` is **commented out** in `config/tenancy.php` — queries never switch connections. But `CacheTenancyBootstrapper`, `FilesystemTenancyBootstrapper`, `QueueTenancyBootstrapper` **are enabled**, so when tenancy is initialized `storage_path()` is suffixed with `tenant{clinic_id}` and cache keys are tagged per-tenant. Expect this when debugging file/cache/queue behavior in a tenant context.

## Access URLs (Local Dev)

```
Central admin:  http://127.0.0.1:8000/admin
Clinic panel:   http://127.0.0.1:8000/app                 (tenant resolved from user clinic_id after login)
Patient portal: http://127.0.0.1:8000/{tenant}/portal/{patient}
```

Production: each clinic accesses via subdomain `clinic1.<central-domain>/app`. Portal routes use `signed` URLs + `throttle:portal` (30 req/min per IP, defined in `AppServiceProvider::boot()`).

## Middleware Chain

**Filament App panel** (`AppPanelProvider`, id `app`) — middleware list ends with:
```
... → InitializeTenancyBySubdomainId → SetTenancyUrlDefaults → SyncSpatiePermissionsTeamId
```
then `Authenticate` in `authMiddleware`.

**Admin panel** (`AdminPanelProvider`, id `admin`, default panel) — standard Filament middleware **plus `SyncSpatiePermissionsTeamId`**, but **no tenancy-init middleware** (it's central).

- `SetTenancyUrlDefaults` falls back to `$request->segment(1)` if `tenant()` is unset, skipping known segments `admin`, `up`, `login`, `register`, `livewire`.
- `SyncSpatiePermissionsTeamId` calls `setPermissionsTeamId($tenantId)` (or `null` in central context) and clears the user's cached permissions/relations — this is what ties Spatie to the current clinic.

## Filament Panels

| Panel | ID | Path | Resources | Theme |
|---|---|---|---|---|
| Admin (central) | `admin` (default) | `/admin` | `app/Filament/Resources/` | `resources/css/filament/admin/theme.css` |
| App (tenant) | `app` | `/app` | `app/Filament/App/Resources/` | `resources/css/filament/app/theme.css` |

Both panels: dark mode, collapsible sidebar, primary color (App=Cyan, Admin=Indigo). Resources/pages/widgets are auto-discovered from the directories above. Vite entry points (from `vite.config.js`): `resources/css/app.css`, `resources/js/app.js`, `resources/css/filament/app/theme.css`, `resources/css/filament/admin/theme.css`.

## Test Conventions

- Base class `Tests\TestCase` (NOT `PHPUnit\Framework\TestCase`) provides:
  - `setUpTenants()` — creates `clinic-a` / `clinic-b`, users (admin/doctor/assistant per clinic), the 4 roles, ~50 `View:Model`-style permissions, and patients. **Call it in `setUp()`** for tests needing tenants.
  - `switchTenant($id)` — `Tenancy::initialize($id)`
  - `actingAsDoctor($user)`, `actingAsAdmin($user)`, `actingAsAssistant($user)`, `actingAsSuperAdmin()` (super-admin has `clinic_id = null`)
  - Factory helpers: `createOdontogram()`, `createClinicalRecord()`, `createBudget()`, `createBudgetWithItems()`, `createAppointment()`, `createPayment()`, `createProcedurePrice()`, `createInventoryItem()`
- Tests use `RefreshDatabase`. Portal tests require `signed` URLs.
- Permission naming convention: `<Action>:<Model>` (e.g. `ViewAny:Patient`, `Create:Budget`).

## Key Directories (non-obvious)

```
app/Filament/App/Resources/      Clinic-panel resources (auto-discovered)
app/Filament/App/Pages/          ClinicSettings page
app/Filament/Resources/          Central admin resources (auto-discovered)
app/Http/Middleware/             InitializeTenancyBySubdomainId, SetTenancyUrlDefaults, SyncSpatiePermissionsTeamId
app/Livewire/                    Odontogram (interactive SVG), Auth/RegisterTenant, PatientPortal/BookAppointment
app/Services/BudgetGenerator.php Auto-generates budgets from completed odontograms
app/Services/TenantService.php   Tenant/onboarding helpers
app/Traits/BelongsToClinic.php   Global-scope trait for tenant models
app/Scopes/ClinicScope.php       Global scope: filters by clinic_id when tenancy initialized
app/Observers/                   OdontogramObserver (budget gen), AppointmentObserver (inventory deduction), ClinicObserver
app/Policies/                    13 policies (RBAC enforcement)
```

## Odontogram & BudgetGenerator (complex logic)

- SVG-based Livewire component, 32 teeth × 6 surfaces. Procedures read dynamically from `procedure_prices` (not hardcoded). `clinical_records.procedure_price_id` links directly to a procedure; `diagnosis_code` is a fallback for color mapping.
- `BudgetGenerator::generate()` runs in a DB transaction (triggered by `OdontogramObserver` when an odontogram's status becomes `completed`):
  1. Returns existing budget if one already has this `odontogram_id` (no duplicates).
  2. Loads clinical records where `treatment_status != 'completed'`.
  3. Resolves price from `procedure_price_id` first, falls back to `diagnosis_code` lookup, then to hardcoded `diagnosisDefaults` (e.g. `caries`, `endodontic`, `crown`).
  4. Groups items by procedure, appends tooth numbers to the treatment name.
  5. Creates a `draft` budget with 30-day `expires_at`; toast notification fired by the observer.

## CI/CD (`.github/workflows/ci.yml`)

- **Tests job**: `composer install` → `npm ci` → `npm run build` → `cp .env.example .env` → `key:generate` → `migrate --force` → `php artisan test`, against `postgres:15` (env: `DB_*` = `pgsql`/`127.0.0.1`/`dentalflow_test`/`postgres`/`postgres`).
- **Code-quality job**: `phpstan analyse` (non-blocking via `|| true`).
- **Security job**: `composer audit` (non-blocking).
- Triggers: push to `main`/`master`/`develop`, PR to `main`/`master`. PHP 8.3, Node 20.
