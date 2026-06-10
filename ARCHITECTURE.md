# DentalFlow SaaS — Arquitectura del Sistema

---

## Diagrama de Alto Nivel

```
┌──────────────────────────────────────────────────────────────────┐
│                         NGINX (Wildcard)                          │
│            *.dentalflow.dev → PHP-FPM (Laravel 12)               │
└────────────────────────┬─────────────────────────────────────────┘
                         │
                         ▼
┌──────────────────────────────────────────────────────────────────┐
│                     MIDDLEWARE CHAIN                              │
│                                                                    │
│  Request → EncryptCookies → StartSession → VerifyCsrfToken        │
│         → SubstituteBindings → DisableBladeIconComponents         │
│         → DispatchServingFilamentEvent                             │
│         → InitializeTenancyBySubdomainId  ← Tenant ID             │
│         → SetTenancyUrlDefaults           ← URL defaults          │
│         → SyncSpatiePermissionsTeamId     ← Permissions sync      │
│         → Authenticate                                            │
└────────────────────────┬─────────────────────────────────────────┘
                         │
          ┌──────────────┴──────────────┐
          ▼                              ▼
┌──────────────────┐          ┌──────────────────┐
│   ADMIN PANEL    │          │    APP PANEL     │
│   (Central)      │          │   (Tenant)       │
│   /admin         │          │   /app            │
│                  │          │                  │
│  No tenant scope │          │  clinic_id scope │
│  5 resources     │          │  9 resources     │
│  1 page          │          │  1 page          │
│  0 widgets       │          │  7 widgets       │
└──────────────────┘          └──────────────────┘
                                      │
                                      ▼
┌──────────────────────────────────────────────────────────────────┐
│                      TENANT ISOLATION                             │
│                                                                    │
│  Global Scope: ClinicScope → WHERE clinic_id = tenant('id')      │
│  Auto-set: BelongsToClinic → creating event                      │
│  Bypass: ->withoutTenancy() or ->withoutGlobalScope()            │
└──────────────────────────────────────────────────────────────────┘
```

---

## Flujo de Request por Tipo de Ruta

### 1. Panel Admin (`/admin`)
```
Cliente → GET /admin/login
  → AdminPanelProvider (default panel, id='admin')
  → NO middleware de tenancy
  → Autenticación normal (sin scope de clinic_id)
  → Super-admin: clinic_id = null, acceso total
```

### 2. Panel App — Subdominio (producción)
```
Cliente → GET clinic1.dentalflow.dev/app/login
  → Nginx: server_name *.dentalflow.dev
  → InitializeTenancyBySubdomainId:
      host = "clinic1.dentalflow.dev"
      parts = ["clinic1", "dentalflow", "dev"]
      subdomain = "clinic1"
      ¿Está en central_domains? NO → continuar
      Clinic::find("clinic1") → encontrado → tenancy()->initialize($tenant)
  → SetTenancyUrlDefaults:
      tenant('id') = "clinic1" → URL::defaults(['tenant' => "clinic1"])
  → SyncSpatiePermissionsTeamId:
      setPermissionsTeamId("clinic1")
      Limpiar caché de permisos del usuario
  → Authenticate → App Panel (scoped)
```

### 3. Panel App — Path (localhost)
```
Cliente → GET localhost/clinic1/app/login
  → InitializeTenancyBySubdomainId:
      host = "localhost"
      ¿localhost/127? → SI → $next($request) (skip tenancy)
      return $next($request)  ← Sin inicializar tenancy
  → SetTenancyUrlDefaults:
      tenant('id') = null → fallback: segment(1) = "clinic1"
      URL::defaults(['tenant' => "clinic1"])
  → SyncSpatiePermissionsTeamId:
      Filament::getTenant() = null
      tenancy()->tenant = null  ← No inicializado aún!
      NO setPermissionsTeamId()
  → Filament redirige a login
  → En login, Filament detecta segment(1) como tenant y lo inicializa
```

### 4. Portal de Paciente
```
Cliente → GET clinic1.dentalflow.dev/portal/{patient}?signature=...
  → Middleware: web + signed + throttle:portal + InitializeTenancyByPath
  → InitializeTenancyByPath:
      segment(1) = "clinic1" → Clinic::find("clinic1") → tenancy()->initialize($tenant)
  → PatientPortalController:
      $patient = Patient::where('id', $patient)
          ->where('clinic_id', tenant('id'))
          ->firstOrFail()
```

---

## Patrón Multi-Tenant (Shared Database)

### Configuración
```php
// TenancyServiceProvider::boot()
BelongsToTenant::$tenantIdColumn = 'clinic_id';
```

### Estructura de Tablas
```sql
-- Todas las tablas tenant tienen clinic_id
patients       (id, clinic_id, name, email, ...)
odontograms    (id, clinic_id, patient_id, ...)
clinical_records (id, clinic_id, odontogram_id, ...)
budgets        (id, clinic_id, patient_id, odontogram_id, ...)
-- etc.

-- Tablas centrales (sin clinic_id)
tenants        (id, name, ...)
domains        (id, tenant_id, domain, ...)
permissions    (id, name, guard_name, ...)
roles          (id, name, guard_name, ...)
```

### Global Scope en Acción
```php
// BelongsToClinic trait boot
static::addGlobalScope(new ClinicScope);
// ClinicScope::apply():
//   WHERE clinic_id = tenant('id')

// Query automáticamente scoped:
Patient::all();  // → WHERE clinic_id = 'clinic1'
Patient::where('name', 'like', '%Juan%')->get();  // → WHERE clinic_id = 'clinic1' AND name LIKE '%Juan%'

// Bypass (super-admin):
Patient::withoutTenancy()->get();  // → sin WHERE clinic_id
Patient::withoutGlobalScope(ClinicScope::class)->get();
```

---

## RBAC (Spatie Permissions)

### Jerarquía
```
super-admin  (clinic_id = NULL)  → Acceso a TODAS las clínicas
  └─ admin      (clinic_id = X)    → Admin de clínica específica
       ├─ doctor    (clinic_id = X)   → CRUD pacientes, citas, odontogramas, presupuestos
       └─ assistant  (clinic_id = X)  → View/Create/Update pacientes, citas, presupuestos
```

### Formato de Permisos
```
Action:Resource

Ejemplos:
  ViewAny:Patient    → Listar pacientes
  View:Patient       → Ver paciente individual
  Create:Patient     → Crear paciente
  Update:Patient     → Editar paciente
  Delete:Patient     → Eliminar paciente
```

### Sincronización con Tenant
```php
// SyncSpatiePermissionsTeamId middleware - se ejecuta en cada request:
setPermissionsTeamId(tenant('id'));
$user->forgetCachedPermissions();  // Limpiar caché
```

---

## Flujo del Odontograma

```
1. Doctor crea Odontograma
   PatientResource → ViewOdontogram → Livewire Odontogram

2. Selecciona diente (SVG click)
   → tooth.blade.php renderiza SVG con color según diagnosis_code

3. Selecciona superficie(s)
   → Panel flotante: elegir procedimiento de procedure_prices (CRUD dinámico)

4. Guarda ClinicalRecord
   → procedure_price_id + diagnosis_code + treatment_status + surface

5. Cambia status a "completed"
   → OdontogramObserver::updated()
   → BudgetGenerator::generate($odontogram):
      a. Verifica duplicados (odontogram_id)
      b. Carga ProcedurePrice en batch (evita N+1)
      c. Itera ClinicalRecords no completados
      d. Precio: procedure_price_id → fallback diagnosis_code → fallback defaults
      e. Agrupa por procedimiento, concatena números de diente
      f. Crea Budget + BudgetItems (expires_at +30 días)
   → Notificación toast: "Presupuesto generado: $XXX.XX"
```

---

## Flujo de Cita y Calendario

```
1. CalendarWidget renderiza citas en calendario (drag-and-drop)

2. Al mover cita (drop):
   → Validación: fecha no en pasado
   → Validación: sin solapamiento con otras citas
   → Actualización de start_time/end_time

3. Al crear cita:
   → AppointmentObserver::created()
   → Deducción automática de inventario (si aplica)

4. Al eliminar cita:
   → Soft delete (no hard delete)
```

---

## Base de Datos

### PostgreSQL (única base compartida)

```
dentalflowsaas (o dentalflow_test para tests)
├── tenants            (tabla principal de Stancl)
├── domains            (dominios por tenant)
├── users              (clinic_id para tenant, NULL para super-admin)
├── permissions        (tablas Spatie)
├── roles
├── model_has_roles
├── model_has_permissions
├── patients           (clinic_id)
├── odontograms        (clinic_id, patient_id)
├── clinical_records   (clinic_id, patient_id, odontogram_id, procedure_price_id)
├── appointments       (clinic_id, patient_id, user_id)
├── budgets            (clinic_id, patient_id, odontogram_id)
├── budget_items       (clinic_id, budget_id, procedure_price_id)
├── treatments         (clinic_id)
├── payments           (clinic_id, budget_id)
├── procedure_prices   (clinic_id, diagnosis_code)
├── inventories        (clinic_id)
├── procedure_inventory (clinic_id)
├── subscription_payments
├── system_activities
├── cache
├── jobs
└── migrations
```

### Índices Clave
- `clinic_id` en todas las tablas tenant (para global scope eficiente)
- `patient_id` + `odontogram_id` en clinical_records
- `odontogram_id` en budgets (para evitar duplicados)
- `email` único por usuario
- `rut` único por clinic_id en patients

---

## Assets y Frontend

### Vite Build Pipeline
```
vite.config.js
├── Entrada 1: resources/css/app.css        (estilos globales)
├── Entrada 2: resources/js/app.js          (JS global)
├── Entrada 3: resources/css/filament/admin/theme.css  (tema panel admin)
└── Entrada 4: resources/css/filament/app/theme.css    (tema panel app)

Plugin: @tailwindcss/vite (Tailwind 4 CSS, sin archivo de configuración)
```

### Livewire
```
Componentes:
├── Odontogram.php/.blade.php         (odontograma SVG interactivo)
│   └── tooth.blade.php               (renderizado SVG de diente individual)
├── Auth/RegisterTenant.php/.blade.php (registro self-onboarding)
└── PatientPortal/BookAppointment.php  (wizard de reserva)
```

---

## Eventos y Observers

| Evento | Observer | Acción |
|---|---|---|
| `Odontogram.updated` (status → completed) | `OdontogramObserver` | `BudgetGenerator::generate()` + toast notification |
| `Appointment.created` | `AppointmentObserver` | Deducción de inventario |
| `Clinic.*` (created, updated, deleted) | `ClinicObserver` | Logging de ciclo de vida |

---

## Despliegue (Ver `DEPLOY.md`)

### Entornos
- **Local**: `localhost/app` (tenant vía `clinic_id` del usuario autenticado)
- **Staging**: subdominio en servidor de prueba
- **Producción**: subdominio wildcard `*.dentalflow.dev` con SSL

### Stack de Producción
```
Nginx (wildcard SSL) → PHP 8.3-FPM → PostgreSQL 15 → Redis (caché/colas)
                                              └→ Supervisor (queue worker)
```
