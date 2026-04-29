# AGENTS.md - DentalFlow SaaS

## Quick Facts
- **Stack**: Laravel 12, Filament 4.x, Livewire 3.x, Tailwind 4, PostgreSQL 14+
- **Multi-tenancy**: Stancl Tenancy 3.9 (domain-based)
- **Auth/RBAC**: Spatie Permissions 6.0
- **Payments**: Laravel Cashier (Stripe)
- **Tests**: 175 passing (359 assertions) across 17 Feature test files

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
- Test credentials in `.env.testing` (added to `.gitignore`)
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
- **Rate limiting**: Portal routes throttled to 30 req/min per IP
- **Key directories**:
  - `app/Filament/App/` — clinic panel resources and widgets
  - `app/Filament/Resources/` — shared Filament resources
  - `app/Livewire/` — Odontogram interactive component, PatientPortal components
  - `app/Models/` — Patient, Odontogram, ClinicalRecord, Budget, Appointment, etc.
  - `app/Observers/` — model observers (Appointment, Odontogram)
  - `app/Policies/` — authorization policies
  - `app/Scopes/` — global scopes for tenant filtering
  - `app/Services/` — `BudgetGenerator` for auto-budget creation
  - `app/Traits/` — `BelongsToClinic`, `HasSpatiePermissions`, `ActivityLogger`

## Build & Assets
- **Vite**: `vite.config.js` with `@tailwindcss/vite` plugin (Tailwind 4)
- **Entry points**: `resources/css/app.css`, `resources/js/app.js`
- Run `npm run dev` (or `composer run dev`) during development

## Testing
- **Test suite**: 17 Feature files, 1 Unit file
- `SecurityTenantIsolationTest` — 9 tenant isolation tests
- `OdontogramFunctionalTest` — odontogram session/record tests
- `PatientAndAppointmentsTest` — patient and appointment workflows
- `AuthorizationRbacTest` — RBAC permission checks
- `SystemReadinessTest` — structural checks (models, middleware, routes)
- `HttpApiTest` — 20 HTTP/API endpoint tests
- `BudgetGeneratorTest` — 7 auto-budget generation tests
- `CalendarWidgetValidationTest` — 6 calendar drag-and-drop validation tests
- Requires PostgreSQL `dentalflow_test` database to exist before running
- Uses `RefreshDatabase` and `$this->switchTenant('clinic-a')`
- 34 redundant tests removed across 11 files
- 39 weak assertions replaced with strong ones (`assertEquals`, `assertCount`)

## Security Fixes Applied (2026-04-21)
- IDOR in PatientPortalController (dashboard, budget acceptance)
- Authorization bypass in OdontogramsRelationManager
- Missing tenant scope in Odontogram queries
- Routes in web.php missing tenancy middleware
- See `SECURITY_AUDIT.md` for details

## Recent Updates (2026-04-29)

### Odontogram & Procedimientos Dinámicos
- **Odontograma lee procedimientos del CRUD**: El formulario del odontograma ahora consulta `procedure_prices` en lugar de usar opciones hardcodeadas
- **Selector muestra nombre + precio**: Cada procedimiento aparece como "Obturación ($50.000)"
- **`procedure_price_id` en `clinical_records`**: Nueva migración guarda referencia directa al procedimiento seleccionado
- **Resolución de `diagnosis_code`**: Al guardar, se resuelve el código de diagnóstico desde el procedimiento para mantener colores correctos
- **40+ colores mapeados**: `$statusColors` expandido para soportar todos los procedimientos (implantes, ortodoncia, prótesis, etc.)
- **Fallback seguro en vista**: `tooth.blade.php` usa función `$getColor()` con fallback gris para códigos sin color definido

### Seeders con Datos Reales
- **`ProcedurePriceSeeder`**: 47 procedimientos reales organizados por especialidad (general, endodoncia, periodoncia, cirugía, implantes, ortodoncia, prótesis, estética, pediatría, radiología)
- **`InventorySeeder`**: 95 items de inventario real (anestesia, restauración, endodoncia, impresión, ortodoncia, bioseguridad, instrumental, farmacia, radiología, blanqueamiento, prótesis, pedodoncia)
- **Demo odontogramas**: 5 pacientes con odontogramas demo y 8 registros clínicos cada uno (caries, restauraciones, endodoncias, coronas)
- **`PermissionSeeder`**: Agregado `Odontogram` a modelos extra para crear permisos CRUD
- **`TenantSeeder`**: Inicializa contexto de tenancy (`tenancy()->initialize()`) antes de llamar seeders

### Generación de Presupuestos Mejorada
- **`BudgetGenerator` usa `procedure_price_id`**: Prioriza el procedimiento exacto del registro clínico antes de buscar por diagnosis_code
- **Notificaciones en `OdontogramObserver`**: Toast con monto del presupuesto al completar odontograma
- **UI en `ViewOdontogram`**: Panel de presupuesto generado (estado, total, items), botón "Ver Presupuesto" o "Generar Presupuesto"
- **Hint en campo status**: Indica que cambiar a "Completed" genera presupuesto automático

### Bug Fixes
- **Odontogram permissions**: Agregados permisos `ViewAny`, `View`, `Create`, `Update`, `Delete`, `Restore`, `ForceDelete` para `Odontogram`
- **Tenant context en seeders**: `ProcedurePriceSeeder` e `InventorySeeder` usan `Clinic::first()` como fallback cuando `tenant('id')` es null
- **`ViewOdontogram` form**: Eliminado `->color('success')` inexistente en Filament 4 Section

## Recent Updates (2026-04-28)

### Bug Fixes
- Added `procedureInventories()` relationship to `ProcedurePrice` model (inventory deduction was broken)
- Added validation to `CalendarWidget::updateAppointment()` (past dates, overlapping slots)
- Added `BelongsToClinic` trait to `BudgetItem` model + migration for `clinic_id` column
- Fixed `BookAppointment` to use procedure duration instead of hardcoded 30min
- Removed duplicate portal routes from `tenant.php` (centralized in `web.php`)
- Fixed `AppointmentResource` query: only doctors filtered by assignment, admin/assistants see all
- Fixed `ActivityLogger` to prioritize `tenant('id')` over `session('tenant_id')`

### Features
- **Auto-budget generation**: `BudgetGenerator` service creates draft budgets from completed odontograms
- `OdontogramObserver` triggers budget generation when status changes to `completed`
- Manual "Generate Budget" action in `OdontogramsRelationManager` for completed odontograms
- `ProcedurePrice` now supports `diagnosis_code` mapping for automatic pricing
- `Budget` model linked to `odontogram` via `odontogram_id` foreign key
- `Budget` model has `notes` field for additional context
- `BudgetResource` UI shows source odontogram link, status colors, and notes column

### Production Readiness
- CI/CD workflow: `.github/workflows/ci.yml` (tests, code quality, security scan)
- Dockerfile: production-ready PHP 8.3-fpm image with `--no-dev` Composer install
- Deploy guide: `DEPLOY.md` (manual, Docker Compose, Forge/Vapor, Nginx, rollback)
- Transactional emails: Budget sent, Appointment reminders, Password reset
- Legal pages: Terms of Service (`/terms`), Privacy Policy (`/privacy`)

### Code Quality
- Added `ActivityLogger` trait to `Odontogram` and `Treatment` models
- Removed empty Schema classes (`PatientForm`, `AppointmentForm`, `BudgetForm`)
- Added `User::appointments()` inverse relationship
- Created `BudgetItemFactory` with automatic `clinic_id` from parent budget
- Updated `ProcedurePriceFactory` with `diagnosis_code` and integer `duration`
- Created `ProcedurePriceSeeder` with 6 default diagnosis-to-procedure mappings
- Added rate limiting (30 req/min) to patient portal routes

## Setup
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=ProcedurePriceSeeder  # seed default procedure prices
npm install
npm run build
php artisan make:filament-user   # create admin user
```

## Production Readiness
- Deploy with `composer install --no-dev --optimize-autoloader`
- Use `npm install && npm run build` for assets
- Set `COMPOSER_FLAGS=--no-dev` in CI/CD or Forge/Vapor
- CI/CD workflows configured in `.github/workflows/ci.yml`
- Docker deployment via `Dockerfile` or `docker-compose.yml` (see `DEPLOY.md`)
