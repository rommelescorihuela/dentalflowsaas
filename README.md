# DentalFlow SaaS

> **Sistema de Gestión Dental Multi-Tenant con Odontograma Interactivo**

![Laravel](https://img.shields.io/badge/Laravel-12.x-red?logo=laravel)
![Filament](https://img.shields.io/badge/Filament-4.x-orange?logo=filament)
![Livewire](https://img.shields.io/badge/Livewire-3.x-pink?logo=livewire)
![PHP](https://img.shields.io/badge/PHP-8.3-blue?logo=php)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-14%2B-blue?logo=postgresql)
![Tests](https://img.shields.io/badge/Tests-189-green)

---

## Características

- **Multi-Tenant**: Aislamiento completo por `clinic_id`, dominios personalizados, self-onboarding
- **Odontograma Interactivo**: SVG con 32 dientes × 6 superficies, procedimientos dinámicos desde CRUD, 40+ colores
- **Presupuesto Automático**: Generación desde odontograma completado con notificación toast
- **Gestión de Citas**: Calendario drag-and-drop, validación de solapamiento y fechas pasadas
- **Portal de Pacientes**: URLs firmadas, rate limiting, aceptación/rechazo de presupuestos
- **Business Intelligence**: Dashboard con KPIs, gráficos de ingresos, crecimiento de pacientes
- **RBAC**: Spatie Permissions con 4 roles (super-admin, admin, doctor, assistant)

---

## Stack

| Capa | Tecnología |
|---|---|
| Backend | Laravel 12, PHP 8.3+ |
| Frontend | Filament 4, Livewire 3, Tailwind 4 |
| Base de datos | PostgreSQL 14+ |
| Multi-tenancy | Stancl Tenancy 3.9 (shared-DB, `clinic_id`) |
| Auth/RBAC | Spatie Permissions 6.0 |
| Pagos | Laravel Cashier (Stripe) |
| Linting | Laravel Pint (defaults) |
| Static Analysis | PHPStan nivel 5 |
| Assets | Vite 7 |

---

## Requisitos

- PHP >= 8.2
- Composer >= 2.0
- Node.js >= 18
- PostgreSQL >= 14
- Git

---

## Instalación Rápida

```bash
git clone https://github.com/rommelescorihuela/dentalflowsaas.git
cd dentalflowsaas

# Opción 1: Script automatizado
composer run setup

# Opción 2: Paso a paso
composer install
cp .env.example .env
# Editar .env: DB_CONNECTION=pgsql + credenciales
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=ProcedurePriceSeeder
php artisan db:seed --class=InventorySeeder
php artisan make:filament-user
npm install && npm run build
```

---

## Comandos

```bash
composer run dev          # server + queue + logs + vite (concurrently)
composer run test         # config:clear + php artisan test
php artisan test --filter=SecurityTenantIsolationTest
php artisan diagnostic:all [--skip-tests]
php artisan tenants:create
php artisan test:routes
```

---

## URLs (Desarrollo Local)

```
Central admin:  http://127.0.0.1:8000/admin
Clinic panel:   http://127.0.0.1:8000/app                 (tenant via user clinic_id)
Patient portal: http://127.0.0.1:8000/{tenant}/portal/{patient}
```

En producción, cada clínica accede vía subdominio: `clinic1.dentalflow.dev/app`

---

## Estructura

```
app/
├── Console/Commands/         # diagnostic:all, test:routes, check:schema, debug:tenant
├── Filament/
│   ├── Resources/            # Panel Admin (5 recursos: Clinics, Users, Roles, etc.)
│   └── App/
│       ├── Resources/        # Panel Clínica (9 recursos: Patients, Budgets, etc.)
│       ├── Pages/            # ClinicSettings
│       └── Widgets/          # Calendar, Stats, Revenue, Inventory alerts (7 widgets)
├── Http/
│   ├── Controllers/          # PatientPortal, ClinicSettings, Legal
│   └── Middleware/           # Tenancy, Permissions, Locale (4 middleware)
├── Livewire/                 # Odontogram, RegisterTenant, BookAppointment
├── Models/                   # 18 modelos
├── Observers/                # OdontogramObserver, AppointmentObserver, ClinicObserver
├── Policies/                 # 13 policies
├── Services/                 # BudgetGenerator, TenantService
├── Traits/                   # BelongsToClinic, HasSpatiePermissions, ActivityLogger
└── Scopes/                   # ClinicScope
```

---

## Testing

- **189 tests** en 22 archivos Feature + 1 Unit
- Base: `Tests\TestCase` con `setUpTenants()`, `switchTenant()`, helpers de autenticación y factories
- Usa `RefreshDatabase` (truncate, no transacciones — PostgreSQL)
- Portal routes requieren `signed` URLs
- Base de datos de test: `dentalflow_test` (hardcodeada en `phpunit.xml`)

---

## CI/CD

GitHub Actions (`.github/workflows/ci.yml`):
- Tests contra PostgreSQL 15
- PHPStan nivel 5 (non-blocking)
- `composer audit` security scan

---

## Documentación

| Archivo | Contenido |
|---|---|
| `CONTEXT.md` | Especificaciones completas del proyecto |
| `AGENTS.md` | Guía para agentes de IA |
| `SECURITY_AUDIT.md` | Historial de vulnerabilidades corregidas |
| `DEPLOY.md` | Guía de despliegue (manual, Docker, Forge) |

---

## Licencia

Proyecto privado para clínicas dentales autorizadas.
