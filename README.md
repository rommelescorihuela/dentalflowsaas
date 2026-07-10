# DentalFlow SaaS

> **Sistema de Gestión Dental Multi-Tenant con Odontograma Interactivo**

![Laravel](https://img.shields.io/badge/Laravel-12.x-red?logo=laravel)
![Filament](https://img.shields.io/badge/Filament-4.x-orange?logo=filament)
![Livewire](https://img.shields.io/badge/Livewire-3.x-pink?logo=livewire)
![PHP](https://img.shields.io/badge/PHP-8.3-blue?logo=php)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-14%2B-blue?logo=postgresql)
![Tests](https://img.shields.io/badge/Tests-34%20archivos-green)

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
| Pagos | Manual (Venezuela: transferencia, Pago móvil, Zelle, Binance) |
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
# Llenar DB con datos demo (clínicas, roles, permisos, super admin)
php artisan db:seed --class=DatabaseSeeder

# Opción 2: Paso a paso
composer install
cp .env.example .env
# Editar .env: DB_CONNECTION=pgsql + credenciales + TENANCY_CENTRAL_DOMAINS
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=DatabaseSeeder
npm install && npm run build
```

> **Super admin por defecto**: `admin@dentalflow.com` / `password`
>
> **Producción**: agregar el dominio principal en `.env`:
> `TENANCY_CENTRAL_DOMAINS=dentalflow.digitalwebsolution.info`

---

## Comandos

```bash
composer run setup        # install + .env + key:generate + migrate + npm install/build
composer run dev          # server + queue + logs + vite (concurrently)
composer run test         # config:clear + php artisan test
composer run lint         # vendor/bin/pint
composer run analyse      # vendor/bin/phpstan analyse app --level=5

php artisan db:seed --class=DatabaseSeeder   # demo: 4 clínicas, roles, permisos, super admin
php artisan migrate:fresh --seed             # reset + seed completo
php artisan diagnostic:all [--skip-tests]    # health check
php artisan test:routes                      # diagnostic de rutas tenant
php artisan subscriptions:process            # ciclo de suscripciones diario
php artisan appointments:send-reminders      # recordatorios de citas
```

---

## URLs

### Desarrollo Local (`127.0.0.1:8000`)

```
Central admin:  http://127.0.0.1:8000/admin
Clinic panel:   http://127.0.0.1:8000/app           (tenant desde clinic_id del usuario)
Patient portal: http://127.0.0.1:8000/{tenant}/portal/{patient}  (URL firmada)
```

### Producción

Cada clínica accede vía subdominio del dominio principal:

```
Central admin:  https://dentalflow.digitalwebsolution.info/admin
Clinic panel:   https://{tenant}.dentalflow.digitalwebsolution.info/app
Patient portal: https://{tenant}.dentalflow.digitalwebsolution.info/{tenant}/portal/{patient}
```

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

- **34 archivos de test** (33 Feature + 1 Unit)
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
