# AGENTS.md — DentalFlow SaaS

## Stack
- **Laravel 12, Filament 4.x, Livewire 3.x, Tailwind 4** via `@tailwindcss/vite`.
- `resources/css/app.css` uses v4 `@import 'tailwindcss'` + `@source`/`@theme`. `tailwind.config.js` exists but is **unused** by the build (IDE only).
- **PostgreSQL ≥14 only** — `.env.example` defaults `DB_CONNECTION=pgsql`.
- **Multi-tenancy**: Stancl Tenancy 3.9, **single shared DB** isolated via `clinic_id` global scope (`ClinicScope`). No connection switching.
- **Auth/RBAC**: Spatie Permissions 6.0 with `teams => true`, `team_foreign_key => 'clinic_id'`. Custom `Permission`/`Role` model classes (configured in `config/permission.php`, registered in `AppServiceProvider::boot()`). 4 roles: `super-admin`, `admin`, `doctor`, `assistant`. Permission naming: `<Action>:<Model>`.
- **Payments**: manual only (Venezuela). `laravel/cashier` in `composer.json` but **NOT used**.
- **Queue**: `database` driver (`.env.example` defaults). `composer run dev` runs `queue:listen`.
- **E2E**: Playwright in `tests/e2e/` (3 spec files).
- **Linting**: Laravel Pint defaults — no `pint.json`. **Static analysis**: PHPStan level 5 — no `phpstan.neon`.

## Artisan Commands

```bash
composer run setup       # install + copy .env + key:generate + migrate + npm install + build (no seed)
composer run dev         # serve + queue:listen --tries=1 + pail --timeout=0 + npm run dev (concurrently)
composer run test        # config:clear + php artisan test
composer run lint        # vendor/bin/pint
composer run analyse     # vendor/bin/phpstan analyse app --level=5

php artisan migrate:fresh --seed           # reset DB + seed 4 demo clinics
php artisan diagnostic:all [--skip-tests]  # system health check
php artisan test:routes                    # route-generation diagnostic for tenant URLs
php artisan subscriptions:process          # daily subscription lifecycle
php artisan appointments:send-reminders    # appointment reminders
php artisan debug:tenant                   # tenant debug dump
php artisan debug:check-schema {table}     # schema inspection
```

## Database & Test Setup
- **PostgreSQL required**. `.env.example` sets `DB_CONNECTION=pgsql`; copy to `.env`, fill `DB_USERNAME`/`DB_PASSWORD`.
- **Test DB**: `dentalflow_test` on `127.0.0.1:5432` (hardcoded in `phpunit.xml`). Credentials from `.env.testing` (gitignored) or env injection. CI uses `postgres`/`postgres`.
- 34 test files (33 Feature + 1 Unit stub). Base class `Tests\TestCase` provides:
  - `setUpTenants()` — creates clinics `clinic-a`/`clinic-b`, Pro subscriptions, 4 roles, permissions, patients. **Call in `setUp()`** for tests needing tenants.
  - `switchTenant($id)`, `actingAsDoctor/Admin/Assistant($user)`, `actingAsSuperAdmin()`
  - Factory helpers: `createOdontogram()`, `createClinicalRecord()`, `createBudget()`, `createBudgetWithItems()`, `createAppointment()`, `createPayment()`, `createProcedurePrice()`, `createInventoryItem()`
- Tests use `RefreshDatabase`. Portal tests require `signed` URLs.
- Super-admin bypasses all authorization via `Gate::before` in `AppServiceProvider::boot()`.

## Tenancy Architecture

| Route group | Middleware | How tenant is identified |
|---|---|---|
| Filament app panel (`/app`) | `InitializeTenancyBySubdomainId` | First host segment split on `.` → tenant ID. Skips `localhost`/`127`. |
| Patient portal | `InitializeTenancyByPath` | URL path: `/{tenant}/portal/{patient}` |
| Central admin (`/admin`) | none | No tenant — central domain only |

- **Local dev (`localhost`)**: App panel at `http://127.0.0.1:8000/app`. Tenancy resolved from logged-in user's `clinic_id` via `ClinicScope`.
- `config('tenancy.central_domains')` = `['localhost', '127.0.0.1']` + `TENANCY_CENTRAL_DOMAINS` env. **Must set in production** (e.g. `dentalflow.digitalwebsolution.info`).
- **Custom `TenancyServiceProvider`** registered in `bootstrap/providers.php`. Stancl's package provider is in `composer.json` `dont-discover` so our provider runs instead. Overrides `BelongsToTenant::$tenantIdColumn = 'clinic_id'`, omits `CreateDatabase`/`MigrateDatabase`/`DeleteDatabase` jobs.
- **Tenant isolation**: 15 models use `BelongsToClinic` trait → adds `ClinicScope`. No-op when `tenancy()->initialized` is false. Bypass with `->withoutTenancy()` or `->withoutGlobalScope(ClinicScope::class)`.
- **Bootstrappers**: `DatabaseTenancyBootstrapper` **commented out** in `config/tenancy.php`. `CacheTenancyBootstrapper`, `FilesystemTenancyBootstrapper`, `QueueTenancyBootstrapper` enabled: `storage_path()` suffixed with `tenant{clinic_id}` when tenancy is initialized.
- **Tenant timezone**: set on `TenancyInitialized` event, reset on `TenancyEnded` (`AppServiceProvider`).
- **Custom models**: `App\Models\Domain` (overrides package `Domain`, uses `clinic_id`), `App\Models\Clinic` (extends `BaseTenant` with `VirtualColumn`).
- **Key enums**: `app/Enums/Plan.php`, `app/Enums/SubscriptionStatus.php`.
- **Clinic IDs**: string-based (not Stancl UUIDs). Tests use `clinic-a`, `clinic-b`.

## Stancl VirtualColumn — Critical
- `Clinic` uses `VirtualColumn` trait. On load, `data` JSON is decoded into individual attributes then **set to `null`**.
- After loading: `$clinic->data` returns `null`. Read/write virtual attributes directly: `$clinic->currency = 'USD'`.
- **Never set `$clinic->data = [...]`** — `encodeAttributes()` on save will overwrite it.
- `$clinic->onboarding_step` accessor falls back to raw `DB::table('tenants')` query when `data` is empty.

## Filament Panels

| Panel | ID | Path | Resources dir |
|---|---|---|---|
| Admin (central, default) | `admin` | `/admin` | `app/Filament/Resources/` |
| App (tenant) | `app` | `/app` | `app/Filament/App/Resources/` |

- Both: dark mode, collapsible sidebar. App=Cyan primary, Admin=Indigo.
- **Filament 4 `Section`** → `Filament\Schemas\Components\Section` (not `Filament\Forms\Components\Section`).
- Resource schemas live in `Schemas/` subdirs (e.g., `Clinics/Schemas/ClinicForm.php`).

## Middleware Chain
- **App panel**: `InitializeTenancyBySubdomainId → SetTenancyUrlDefaults → SyncSpatiePermissionsTeamId → Authenticate → EnsureSubscriptionActive`
- **Admin panel**: standard Filament + `SyncSpatiePermissionsTeamId` (no tenancy)
- All users log in at `/admin/login` (central Filament). The user's `clinic_id` + `SyncSpatiePermissionsTeamId` scope them to their tenant after login.
- `AppServiceProvider::boot()` sets `URL::defaults(['tenant' => ...])` from path segment 1 and initializes tenancy for Livewire updates via referer header.

## Gotchas
- No `database/migrations/tenant/` directory; all shared-DB migrations are in `database/migrations/`. `config/tenancy.php:196` points to a dir that doesn't exist.
- Portal routes use `signed` URLs + `throttle:portal` (30 req/min per IP).
- `tailwind.config.js` exists but **not used by build** (Tailwind v4 via `@tailwindcss/vite` reads CSS `@source` directives).
- `config/permission.php` has `'teams' => true` and `'team_foreign_key' => 'clinic_id'`.
- **`ActivityLogger` trait** (used by User, Clinic, Appointment, etc.) logs every `created`/`updated`/`deleted` to `system_activities` table. Generates DB records on every mutation.
- **`InitializeTenancyBySubdomainId`** is custom middleware (not Stancl's). It uses the subdomain **as the tenant ID directly** (string match on `tenants.id`), not via the `domains` table.
- **Observers**: `OdontogramObserver` auto-generates a budget on `completed` status. `AppointmentObserver` deducts inventory on `completed` status (registered inline in `Appointment::boot()`, not in `AppServiceProvider`).
- **`SubscriptionService::syncClinicDenormalized()`** writes `plan` + `subscription_status` directly to `tenants` table (duplicated data beyond the `Subscription` model).
- **`ClinicSettingsController::save()`** writes directly to `DB::table('tenants')` bypassing Eloquent.
- **Real `.env` defaults**: `APP_LOCALE=es`, `SESSION_DRIVER=file`, `TENANCY_CENTRAL_DOMAINS=localhost:8000,127.0.0.1:8000` (differs from `.env.example`).
- **`PermissionSeeder`** auto-discovers Filament Resources to generate permissions (7 per model: `ViewAny`..`ForceDelete`). Run `setPermissionsTeamId($clinic->id)` before seeding within tenant context.
