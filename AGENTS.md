# AGENTS.md - DentalFlow SaaS

## Quick Facts
- **Stack**: Laravel 12, Filament 4.x, Livewire 3.x, PostgreSQL 14+
- **Multi-tenancy**: Stancl Tenancy 3.9
- **Auth**: Spatie Permissions 6.0
- **Tests**: 48 passing (88 assertions)

## Critical Commands
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --filter=SecurityTenantIsolationTest

# Full system diagnostic
php artisan diagnostic:all

# Route diagnostic
php artisan test:routes
```

## Database
- **Engine**: PostgreSQL (NOT SQLite)
- **Config**: `phpunit.xml` uses pgsql with database `dentalflow_test`
- Do NOT use SQLite for testing

## Architecture
- **Tenant isolation**: Each clinic has `clinic_id`; models use `BelongsToClinic` trait
- **Permission sync**: `SyncSpatiePermissionsTeamId` middleware syncs permissions with clinic
- **Panels**: `/app` (clinic panel), `/admin` (admin panel), `/portal` (patient portal)

## Security Fixes Applied (2026-04-21)
- IDOR in PatientPortalController (dashboard, budget acceptance)
- Authorization bypass in OdontogramsRelationManager
- Missing tenant scope in Odontogram queries
- Routes in web.php missing tenancy middleware
- See `SECURITY_AUDIT.md` for details

## Key Files
- `CONTEXT.md` - Project overview
- `README.md` - Full documentation
- `SECURITY_AUDIT.md` - Security audit
- `app/Console/Commands/SystemDiagnosticCommand.php` - Diagnostic command
- `tests/Feature/` - Test suites

## Setup
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```