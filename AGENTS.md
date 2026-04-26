# AGENTS.md - DentalFlow SaaS

## Quick Facts
- **Stack**: Laravel 12, Filament 4.x, Livewire 3.x, Tailwind 4, PostgreSQL 14+
- **Multi-tenancy**: Stancl Tenancy 3.9 (domain-based)
- **Auth/RBAC**: Spatie Permissions 6.0
- **Payments**: Laravel Cashier (Stripe)
- **Tests**: 48 passing (88 assertions) across 6 Feature test files

## Critical Commands
```bash
# Dev server (server + queue + logs + vite)
composer run dev

# Run all tests
composer run test
# or: php artisan test

# Run a single test
php artisan test --filter=SecurityTenantIsolationTest

# Full system diagnostic
php artisan diagnostic:all
php artisan diagnostic:all --skip-tests

# Setup from scratch
composer run setup
```

## Database
- **Engine**: PostgreSQL only — do NOT use SQLite
- **Test DB**: `dentalflow_test` on `127.0.0.1:5432` (see `phpunit.xml`)
- Test credentials in `phpunit.xml` must match your local PostgreSQL setup

## Architecture
- **Middleware chain**: `InitializeTenancyByDomain` → `SetTenancyUrlDefaults` → `SyncSpatiePermissionsTeamId` → `ForceOnboardingMiddleware`
- **Tenant identification**: By domain in production; by path (`{tenant}`) on localhost/127.0.0.1
- **Access URLs on local dev**:
  - Clinic panel: `http://127.0.0.1:8000/{tenant}/app` (e.g. `/clinic1/app`)
  - Admin panel: `http://127.0.0.1:8000/admin` (central, no tenant prefix)
  - Patient portal: `http://127.0.0.1:8000/{tenant}/portal/{patient}`
- **Existing tenants**: `clinic1`, `clinic2` (see `domains` table for custom domains)
- **Tenant isolation**: Models use `BelongsToClinic` trait with global scopes filtering by `clinic_id`
- **Permission sync**: `SyncSpatiePermissionsTeamId` middleware ties Spatie permissions to clinic
- **Key directories**:
  - `app/Filament/App/` — clinic panel resources and widgets
  - `app/Filament/Resources/` — shared Filament resources
  - `app/Livewire/` — Odontogram interactive component, PatientPortal components
  - `app/Models/` — Patient, Odontogram, ClinicalRecord, Budget, Appointment, etc.
  - `app/Observers/` — model observers
  - `app/Policies/` — authorization policies
  - `app/Scopes/` — global scopes for tenant filtering
  - `app/Traits/` — `BelongsToClinic`, `HasSpatiePermissions`, `ActivityLogger`

## Build & Assets
- **Vite**: `vite.config.js` with `@tailwindcss/vite` plugin (Tailwind 4)
- **Entry points**: `resources/css/app.css`, `resources/js/app.js`
- Run `npm run dev` (or `composer run dev`) during development

## Testing
- **Test suite**: 6 Feature files, no Unit tests currently
- `SecurityTenantIsolationTest` — 9 tenant isolation tests
- `OdontogramFunctionalTest` — odontogram session/record tests
- `PatientAndAppointmentsTest` — patient and appointment workflows
- `AuthorizationRbacTest` — RBAC permission checks
- `SystemReadinessTest` — structural checks (models, middleware, routes)
- Requires PostgreSQL `dentalflow_test` database to exist before running

## Security Fixes Applied (2026-04-21)
- IDOR in PatientPortalController (dashboard, budget acceptance)
- Authorization bypass in OdontogramsRelationManager
- Missing tenant scope in Odontogram queries
- Routes in web.php missing tenancy middleware
- See `SECURITY_AUDIT.md` for details

## Setup
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan make:filament-user   # create admin user
```
