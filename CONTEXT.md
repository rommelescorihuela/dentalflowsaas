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
| `Budget` | `budgets` | Presupuestos (con odontogram_id, notes) |
| `BudgetItem` | `budget_items` | Items de presupuesto (con clinic_id) |
| `Treatment` | `treatments` | Tratamientos disponibles |
| `Domain` | `domains` | Dominios personalizados por tenant |
| `SystemActivity` | `system_activities` | Log de actividades |
| `Payment` | `payments` | Pagos |
| `SubscriptionPayment` | `subscription_payments` | Pagos de suscripción |
| `ProcedurePrice` | `procedure_prices` | Precios de procedimientos (con diagnosis_code) |
| `Inventory` | `inventories` | Inventario (95 items reales por clínica) |
| `ProcedureInventory` | `procedure_inventory` | Inventario de procedimientos |
| `ClinicalRecord` | `clinical_records` | Registro clínico (con procedure_price_id) |

---

## Odontograma Interactivo (Feature Principal)

- **SVG interactivo** con 32 dientes (18 superiores + 18 inferiores, num 11-48)
- **6 superficies por diente**: top, bottom, left, right, center, root
- **Multi-selección** de superficies para tratamientos en lote
- **Procedimientos dinámicos**: El selector lee directamente de `procedure_prices` (CRUD), mostrando nombre + precio
- **`procedure_price_id`**: Cada registro clínico guarda referencia al procedimiento exacto seleccionado
- **40+ colores mapeados**: Soporte para todos los procedimientos (implantes, ortodoncia, prótesis, etc.)
- **Fallback seguro**: `tooth.blade.php` usa `$getColor()` con fallback gris para códigos sin color
- **Panel flotante** no bloqueante para edición
- **Historial por sesiones** - múltiples odontogramas por paciente
- **Presupuesto automático** al completar odontograma
- **Trait**: `BelongsToClinic` para aislamiento multi-tenant

### Códigos de Diagnóstico (Principales)
| Código | Color | Descripción |
|--------|-------|-------------|
| `caries` | 🔴 #ef4444 | Caries |
| `filled` | 🔵 #3b82f6 | Restauración/Empaste |
| `endodontic` | 🟡 #eab308 | Tratamiento Endodóntico |
| `missing` | ⚫ #1f2937 | Pieza Faltante |
| `crown` | 🟣 #a855f7 | Corona |
| `healthy` | ⚪ #ffffff | Sano |

### Códigos Adicionales (40+)
`prophylaxis`, `sealant`, `fluoride`, `inlay`, `scaling`, `gingivectomy`, `flap_surgery`, `surgical_extraction`, `wisdom_tooth`, `apicoectomy`, `frenectomy`, `implant`, `implant_crown`, `sinus_lift`, `braces_metal`, `braces_aesthetic`, `ortho_adjustment`, `retainer_fixed`, `retainer_removable`, `crown_pfm`, `crown_zirconia`, `bridge`, `partial_denture`, `full_denture`, `denture_rebase`, `whitening`, `veneer_composite`, `veneer_ceramic`, `gingival_contouring`, `ss_crown`, `pulpotomy`, `space_maintainer`, `consultation`, `xray_periapical`, `xray_panoramic`, `cbct`

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
php artisan test --filter=BudgetGeneratorTest
php artisan test --filter=CalendarWidgetValidationTest

# Rutas
php artisan test:routes
php artisan route:list

# Tenancy
php artisan tenants:create
php artisan tenants:migrate
php artisan tenants:artisan

# Seeders
php artisan db:seed --class=ProcedurePriceSeeder  # 47 procedimientos reales
php artisan db:seed --class=InventorySeeder        # 95 items de inventario real

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
│       │   │   │   └── OdontogramsRelationManager.php  # "Generate Budget"
│       │   │   └── Pages/
│       │   │       └── ViewOdontogram.php
│       │   ├── Budgets/
│       │   │   └── BudgetResource.php  # Link odontograma, notas, colores
│       │   └── SystemActivities/
│       └── Widgets/
│           └── CalendarWidget.php  # Validación drag-and-drop
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
│       └── BookAppointment.php         # Reservar citas (duración dinámica)
├── Models/
│   ├── Patient.php
│   ├── Odontogram.php                 # ActivityLogger añadido
│   ├── ClinicalRecord.php
│   ├── Budget.php                     # odontogram_id, notes
│   ├── BudgetItem.php                 # BelongsToClinic añadido
│   └── ProcedurePrice.php             # diagnosis_code, procedureInventories()
├── Observers/
│   ├── AppointmentObserver.php        # Deducción de inventario
│   └── OdontogramObserver.php         # Generación automática de presupuestos
├── Services/
│   └── BudgetGenerator.php            # Servicio de generación de presupuestos
├── Providers/
│   ├── TenancyServiceProvider.php
│   └── AppServiceProvider.php         # Rate limiting, observers
└── Traits/
    ├── BelongsToClinic.php
    ├── HasSpatiePermissions.php
    └── ActivityLogger.php
```

---

## Testing (175 tests, 359 aserciones)

### Suites de Tests
- `SecurityTenantIsolationTest` - 9 tests de aislamiento
- `OdontogramFunctionalTest` - 10 tests del odontograma
- `PatientAndAppointmentsTest` - 10 tests de pacientes
- `AuthorizationRbacTest` - 8 tests de autorización
- `SystemReadinessTest` - 6 tests de sistema
- `HttpApiTest` - 20 tests HTTP/API
- `BudgetGeneratorTest` - 7 tests de generación automática de presupuestos
- `CalendarWidgetValidationTest` - 6 tests de validación de calendario
- 34 tests redundantes eliminados
- 39 aserciones débiles reemplazadas por fuertes

### Ejecución
```bash
php artisan test
php artisan test --filter=SecurityTenantIsolationTest
php artisan test --filter=BudgetGeneratorTest
php artisan test --filter=CalendarWidgetValidationTest
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

## Actualizaciones Recientes (2026-04-28)

### Validaciones y Restricciones
- Validación de fechas pasadas en `Appointment.php`
- Validación de solapamiento de horarios en `Appointment.php`
- RUT único por clínica (migración `2026_04_27_195120`)
- Validación en `CalendarWidget::updateAppointment()` (drag-and-drop)

### Presupuesto Automático
- `BudgetGenerator` service genera presupuestos desde odontogramas completados
- `OdontogramObserver` dispara generación al cambiar status a `completed`
- Botón "Generate Budget" manual en `OdontogramsRelationManager`
- `ProcedurePrice` con `diagnosis_code` para mapeo automático
- `Budget` con `odontogram_id` y campo `notes`
- `BudgetResource` UI mejorada con link a odontograma y colores por estado

### Testing y Seguridad
- `.env.testing` creado y añadido a `.gitignore`
- 34 tests redundantes eliminados
- 39 aserciones débiles reemplazadas por fuertes
- 20 nuevos tests HTTP/API (`HttpApiTest.php`)
- 7 tests de generación automática (`BudgetGeneratorTest.php`)
- 6 tests de validación de calendario (`CalendarWidgetValidationTest.php`)
- Health check `/up` configurado en `bootstrap/app.php`
- `require-dev` correctamente aislado en `composer.json`
- Rate limiting en portal (30 req/min por IP)

### Producción
- CI/CD: `.github/workflows/ci.yml` (tests, code quality, security scan)
- Docker: `Dockerfile` (PHP 8.3-fpm, production-ready)
- Guía de despliegue: `DEPLOY.md` (manual, Docker Compose, Forge/Vapor, Nginx, rollback)
- **Emails transaccionales**: Presupuesto enviado, recordatorio de citas, reset de contraseña
- **Legal**: Términos de Servicio (`/terms`) y Política de Privacidad (`/privacy`)

### Code Quality
- `ActivityLogger` añadido a `Odontogram` y `Treatment`
- Eliminadas clases Schema vacías (`PatientForm`, `AppointmentForm`, `BudgetForm`)
- Añadida relación `User::appointments()`
- Creada `BudgetItemFactory` con `clinic_id` automático
- Actualizada `ProcedurePriceFactory` con `diagnosis_code` y `duration` integer
- Creado `ProcedurePriceSeeder` con 6 mapeos diagnosis→procedimiento

## Actualizaciones Recientes (2026-04-29)

### Odontograma Dinámico
- **Procedimientos desde CRUD**: El odontograma ahora lee `procedure_prices` en lugar de opciones hardcodeadas
- **Migración `procedure_price_id`**: Nueva columna en `clinical_records` para vincular al procedimiento exacto
- **40+ colores**: `$statusColors` expandido para todos los procedimientos del catálogo
- **Fallback seguro**: `tooth.blade.php` usa `$getColor()` con fallback gris para evitar errores

### Seeders con Datos Reales
- **`ProcedurePriceSeeder`**: 47 procedimientos organizados por especialidad (general, endodoncia, periodoncia, cirugía, implantes, ortodoncia, prótesis, estética, pediatría, radiología)
- **`InventorySeeder`**: 95 items de inventario real (anestesia, restauración, endodoncia, impresión, ortodoncia, bioseguridad, instrumental, farmacia, radiología, blanqueamiento, prótesis, pedodoncia)
- **Demo odontogramas**: 5 pacientes con odontogramas y 8 registros clínicos demo cada uno
- **Contexto de tenancy**: `TenantSeeder` inicializa `tenancy()->initialize()` antes de llamar seeders

### Presupuesto Automático Mejorado
- **`BudgetGenerator`**: Ahora prioriza `procedure_price_id` del registro clínico antes de buscar por `diagnosis_code`
- **`OdontogramObserver`**: Envía notificación toast con el monto del presupuesto generado
- **`ViewOdontogram`**: Panel de presupuesto generado (estado, total, items), botón "Ver Presupuesto" o "Generar Presupuesto"

### Bug Fixes
- **Permisos de Odontogram**: Agregados 7 permisos CRUD (`ViewAny`, `View`, `Create`, `Update`, `Delete`, `Restore`, `ForceDelete`)
- **Tenant context en seeders**: `ProcedurePriceSeeder` e `InventorySeeder` usan `Clinic::first()` como fallback
- **`ViewOdontogram`**: Eliminado `->color('success')` inexistente en Filament 4 Section

---

## Estado del Sistema (2026-04-29)

### Salud
```
✅ Base de datos: OK
✅ Clínicas: 2 activas (clinic1, clinic2)
✅ Usuarios: Dr. House (clinic1), Dr. Strange (clinic2)
```

### Datos Demo
```
✅ Procedimientos: 47 por clínica (catálogo completo)
✅ Inventario: 95 items por clínica (datos reales)
✅ Odontogramas: 5 con registros demo (clinic1)
✅ Registros Clínicos: 40 demo (caries, restauraciones, endodoncias, coronas)
```

### Features
```
✅ Onboarding: OK
✅ Patient Portal: 18 slots
✅ BI Dashboard: 3 KPIs
✅ Tenant Isolation: OK
✅ Odontogram: OK (procedimientos dinámicos desde CRUD)
✅ Presupuesto automático: OK (con notificación toast)
✅ Rate limiting portal: OK
✅ Permisos Odontogram: OK (7 permisos CRUD)
```
✅ Odontogram: OK
✅ RUT único por clínica: OK
✅ Validación de citas: OK
✅ Presupuesto automático: OK
✅ Rate limiting portal: OK
```

### Benchmark (Promedio: 35-40ms)
```
🚀 Excelente rendimiento
```

### Tests
```
Tests: 175 passed, 359 assertions ✅
Duration: ~35s
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
