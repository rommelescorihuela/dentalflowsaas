# DentalFlow SaaS - Contexto del Proyecto

> Sistema de Gestión Dental Multi-Tenant con Odontograma Interactivo

---

## Stack Principal

- **Backend**: Laravel 12.x, PHP 8.2+
- **Frontend**: Filament 4.x, Livewire 3.x
- **Base de datos**: PostgreSQL 14+
- **Multi-tenancy**: Stancl Tenancy 3.9
- **Auth/RBAC**: Spatie Permissions 6.0

---

## Modelos Principales

| Modelo | Tabla | Descripción |
|--------|-------|-------------|
| `Clinic` | `tenants` | Tenant (clínica) |
| `User` | `users` | Usuarios con roles por clínica |
| `Patient` | `patients` | Paciente con historial médico, alergias, RUT |
| `Odontogram` | `odontograms` | Sesión de odontograma (status: in_progress/completed) |
| `ClinicalRecord` | `clinical_records` | Registro clínico por superficie dental |
| `Appointment` | `appointments` | Citas de pacientes |
| `Budget` | `budgets` | Presupuestos |
| `BudgetItem` | `budget_items` | Items de presupuesto |
| `Treatment` | `treatments` | Tratamientos disponibles |
| `Domain` | `domains` | Dominios personalizados por tenant |
| `SystemActivity` | `system_activities` | Log de actividades |
| `Payment` | `payments` | Pagos |
| `SubscriptionPayment` | `subscription_payments` | Pagos de suscripción |
| `ProcedurePrice` | `procedure_prices` | Precios de procedimientos |
| `Inventory` | `inventories` | Inventario |
| `ProcedureInventory` | `procedure_inventory` | Inventario de procedimientos |

---

## Odontograma Interactivo (Feature Principal)

- **SVG interactivo** con 32 dientes (18 superiores + 18 inferiores, num 11-48)
- **6 superficies por diente**: top, bottom, left, right, center, root
- **Multi-selección** de superficies para tratamientos en lote
- **Diagnósticos**: caries (rojo), filled (azul), endodontic (amarillo), missing (negro), crown (púrpura), healthy (blanco)
- **Panel flotante** no bloqueante para edición
- **Historial por sesiones** - múltiples odontogramas por paciente
- **Trait**: `BelongsToClinic` para aislamiento multi-tenant

### Códigos de Diagnóstico
| Código | Color | Descripción |
|--------|-------|-------------|
| `caries` | 🔴 #ef4444 | Caries |
| `filled` | 🔵 #3b82f6 | Restauración/Empaste |
| `endodontic` | 🟡 #eab308 | Tratamiento Endodóntico |
| `missing` | ⚫ #1f2937 | Pieza Faltante |
| `crown` | 🟣 #a855f7 | Corona |
| `healthy` | ⚪ #ffffff | Sano |

---

## Arquitectura Multi-Tenant

### Ciclo de Request
```
Request → Tenant ID (subdomain/path) → InitializeTenancyByDomain → SetTenancyUrlDefaults → SyncSpatiePermissionsTeamId → ForceOnboardingMiddleware → App
```

### Middleware
- `InitializeTenancyByDomain` - Identifica clínica por subdominio
- `PreventAccessFromCentralDomains` - Bloquea acceso desde dominio principal
- `SyncSpatiePermissionsTeamId` - Sincroniza permisos Spatie con clinic_id
- `ForceOnboardingMiddleware` - Obliga onboarding

### Traits
- `BelongsToClinic` - Asocia modelos a clínica
- `HasSpatiePermissions` - Permisos por clínica
- `ActivityLogger` - Log de actividades

### Roles
- Doctor, Asistente, Admin (por clínica)

---

## Rutas y Paneles

| Ruta | Descripción |
|------|-------------|
| `/` | Landing page pública |
| `/register` | Registro de nueva clínica |
| `/login` | Login general |
| `/admin` | Panel Admin Filament - Gestión de clínicas |
| `/app` | Panel App Filament - Panel principal de la clínica |
| `/portal` | Patient Portal - Portal público para pacientes |
| `/up` | Health check |

---

## Comandos Útiles

```bash
# Diagnóstico completo
php artisan diagnostic:all
php artisan diagnostic:all --skip-tests

# Tests
php artisan test
php artisan test --filter=SecurityTenantIsolationTest

# Rutas
php artisan test:routes
php artisan route:list

# Tenancy
php artisan tenants:create
php artisan tenants:migrate
php artisan tenants:artisan

# Filament
php artisan make:filament-user
php artisan make:filament-resource

# Limpieza
php artisan optimize:clear
```

---

## Archivos de Verificación

| Archivo | Función |
|---------|---------|
| `benchmark.php` | Rendimiento de rutas |
| `verify_system_health.php` | Salud general del sistema |
| `verify_all_phases.php` | Features por fases |
| `verify_registration.php` | Registro de clínicas |

---

## Estructura de Archivos Clave

```
app/
├── Console/Commands/
│   ├── SystemDiagnosticCommand.php  # diagnóstico unificado
│   └── TestRoutesCommand.php          # diagnóstico de rutas
├── Filament/
│   ├── AdminPanelProvider.php       # Provider panel admin
│   ├── AppPanelProvider.php          # Provider panel clínica
│   └── App/
│       ├── Resources/
│       │   ├── Patients/
│       │   │   ├── PatientResource.php
│       │   │   ├── RelationManagers/
│       │   │   │   └── OdontogramsRelationManager.php
│       │   │   └── Pages/
│       │   │       └── ViewOdontogram.php
│       │   ├── Budgets/
│       │   └── SystemActivities/
│       └── Widgets/
├── Http/
│   ├── Controllers/
│   │   └── PatientPortalController.php
│   └── Middleware/
│       ├── SyncSpatiePermissionsTeamId.php
│       ├── ForceOnboardingMiddleware.php
│       └── SetTenancyUrlDefaults.php
├── Livewire/
│   ├── Odontogram.php                 # Componente odontograma
│   └── PatientPortal/
│       └── BookAppointment.php         # Reservar citas
├── Models/
│   ├── Patient.php
│   ├── Odontogram.php
│   ├── ClinicalRecord.php
│   ├── Budget.php
│   └── BudgetItem.php
├── Providers/
│   ├── TenancyServiceProvider.php
│   └── AppServiceProvider.php
├── Services/
│   └── TenantService.php
└── Traits/
    ├── BelongsToClinic.php
    ├── HasSpatiePermissions.php
    └── ActivityLogger.php
```

---

## Testing (48 tests, 88 aserciones)

### Suites de Tests
- `SecurityTenantIsolationTest` - 9 tests de aislamiento
- `OdontogramFunctionalTest` - 10 tests del odontograma
- `PatientAndAppointmentsTest` - 10 tests de pacientes
- `AuthorizationRbacTest` - 8 tests de autorización
- `SystemReadinessTest` - 6 tests de sistema
- `ExampleTest` - 4 tests de ejemplo

### Ejecución
```bash
php artisan test
php artisan test --filter=SecurityTenantIsolationTest
```

---

## Correcciones de Seguridad (2026-04-21)

### Vulnerabilidades Corregidas
| # | Vulnerabilidad | Severidad | Ubicación |
|---|-------------|----------|----------|
| 1 | IDOR Patient Portal Dashboard | 🔴 CRÍTICA | PatientPortalController.php |
| 2 | IDOR Budget Acceptance | 🔴 CRÍTICA | PatientPortalController.php |
| 3 | Authorization Bypass | 🔴 CRÍTICA | OdontogramsRelationManager.php |
| 4 | Missing Tenant Scope | 🟠 ALTA | Odontogram.php |
| 5 | Portal Routes Sin Middleware | 🟠 ALTA | routes/web.php |
| 6 | Soft Deletes Sin Verificación | 🟡 MEDIA | OdontogramsRelationManager.php |

---

## Estado del Sistema (2026-04-21)

### Salud
```
✅ Base de datos: OK
✅ Clínicas: 8 activas
✅ Usuarios: 9 registrados
```

### Features
```
✅ Onboarding: OK
✅ Patient Portal: 18 slots
✅ BI Dashboard: 3 KPIs
✅ Tenant Isolation: OK
✅ Odontogram: OK
```

### Benchmark (Promedio: 35-40ms)
```
🚀 Excelente rendimiento
```

---

## Variables de Entorno Clave

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=dentalflowsaas
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña

TENANCY_AUTO_CREATE_TENANT_DOMAIN=false
TENANCY_DATABASE_PREFIX_ENABLED=false
```