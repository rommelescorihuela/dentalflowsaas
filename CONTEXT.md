# DentalFlow SaaS — Especificaciones del Proyecto

> Sistema de Gestión Dental Multi-Tenant con Odontograma Interactivo
> Última actualización: Junio 2026

---

## Stack Tecnológico

| Componente | Versión | Notas |
|---|---|---|
| Laravel | 12.x | Framework base |
| PHP | 8.3+ | production (`composer.json` pide ^8.2, CI y Docker usan 8.3) |
| Filament | 4.x | Panel admin + panel clínica |
| Livewire | 3.x | Componentes interactivos (Odontograma, RegisterTenant, BookAppointment) |
| Tailwind | 4.x | Vía plugin `@tailwindcss/vite` (sin archivo de configuración) |
| PostgreSQL | ≥14 | Obligatorio; CI usa 15 |
| Stancl Tenancy | 3.9 | Multi-tenancy con shared-DB (`clinic_id`) |
| Spatie Permissions | 6.0 | RBAC sin Filament Shield (gestión de roles personalizada) |
| Laravel Cashier | 16.x | Pagos Stripe |
| Laravel Pint | 1.x | Linting (sin `pint.json` — defaults de Laravel) |
| PHPStan | 2.x | Nivel 5, sin `phpstan.neon` |
| Vite | 7.x | Build de assets frontend |

---

## Modelos (18)

| Modelo | Tabla | Trait | Notas |
|---|---|---|---|
| `Appointment` | `appointments` | BelongsToClinic | Validación de solapamiento y fechas pasadas |
| `Budget` | `budgets` | BelongsToClinic | `odontogram_id`, `notes`, `expires_at` (30 días) |
| `BudgetItem` | `budget_items` | BelongsToClinic | `procedure_price_id`, `clinic_id` |
| `Clinic` | `tenants` | — | Modelo tenant de Stancl; usa `clinic_id` como FK |
| `ClinicalRecord` | `clinical_records` | BelongsToClinic | `procedure_price_id`, `diagnosis_code`, `treatment_status` |
| `Domain` | `domains` | — | Dominios personalizados por tenant |
| `Inventory` | `inventories` | BelongsToClinic | 95 items reales por clínica |
| `Odontogram` | `odontograms` | BelongsToClinic, ActivityLogger | Status: `in_progress` / `completed` |
| `Patient` | `patients` | BelongsToClinic | RUT único por clínica; `doctor_id` |
| `Payment` | `payments` | BelongsToClinic | Integrado con presupuestos |
| `Permission` | `permissions` | — | Modelo Spatie; permisos granulares `Action:Resource` |
| `ProcedureInventory` | `procedure_inventory` | BelongsToClinic | Relación procedimiento↔inventario |
| `ProcedurePrice` | `procedure_prices` | BelongsToClinic | `diagnosis_code`, `code`, `duration`, `description` |
| `Role` | `roles` | — | Modelo Spatie; 4 roles: super-admin, admin, doctor, assistant |
| `SubscriptionPayment` | `subscription_payments` | — | Pagos de suscripción Stripe |
| `SystemActivity` | `system_activities` | — | Log de actividades del sistema |
| `Treatment` | `treatments` | BelongsToClinic, ActivityLogger | Tratamientos disponibles |
| `User` | `users` | BelongsToClinic, HasSpatiePermissions | `clinic_id`; super-admin tiene `clinic_id = null` |

---

## Recursos Filament

### Panel Admin (Central) — 5 recursos
`app/Filament/Resources/`

| Recurso | Ruta | Funcionalidad |
|---|---|---|
| Clinics | `/admin/clinics` | CRUD tenants + RelationManager de dominios |
| Users | `/admin/users` | CRUD usuarios |
| Roles | `/admin/roles` | CRUD roles y permisos Spatie |
| SubscriptionPayments | `/admin/subscription-payments` | CRUD pagos de suscripción |
| SystemActivities | `/admin/system-activities` | Vista de log de actividades |

### Panel App (Clínica) — 9 recursos
`app/Filament/App/Resources/`

| Recurso | Ruta | Funcionalidad |
|---|---|---|
| Patients | `/app/patients` | CRUD pacientes + odontogramas, health progress |
| Appointments | `/app/appointments` | CRUD citas |
| Budgets | `/app/budgets` | CRUD presupuestos + link a odontograma |
| Payments | `/app/payments` | CRUD pagos |
| ProcedurePrices | `/app/procedure-prices` | CRUD precios de procedimientos |
| Inventory | `/app/inventory` | CRUD inventario |
| Users | `/app/users` | CRUD usuarios (scoped a la clínica) |
| Roles | `/app/roles` | CRUD roles (scoped a la clínica) |
| SystemActivities | `/app/system-activities` | Vista de log (scoped a la clínica) |

### Páginas Filament (2)

| Panel | Página | Archivo |
|---|---|---|
| Admin | SystemTools | `app/Filament/Pages/SystemTools.php` |
| App | ClinicSettings | `app/Filament/App/Pages/ClinicSettings.php` |

### Widgets del Panel App (7)

| Widget | Archivo |
|---|---|
| CalendarWidget | `app/Filament/App/Widgets/CalendarWidget.php` |
| StatsOverview | `app/Filament/App/Widgets/StatsOverview.php` |
| FinancialStatsOverview | `app/Filament/App/Widgets/FinancialStatsOverview.php` |
| RevenueChart | `app/Filament/App/Widgets/RevenueChart.php` |
| PatientGrowthChart | `app/Filament/App/Widgets/PatientGrowthChart.php` |
| TodayAppointmentsWidget | `app/Filament/App/Widgets/TodayAppointmentsWidget.php` |
| LowInventoryAlertWidget | `app/Filament/App/Widgets/LowInventoryAlertWidget.php` |

---

## Livewire (3 componentes)

| Componente | Archivo | Función |
|---|---|---|
| Odontogram | `app/Livewire/Odontogram.php` | SVG interactivo 32 dientes × 6 superficies |
| RegisterTenant | `app/Livewire/Auth/RegisterTenant.php` | Registro self-onboarding de clínicas |
| BookAppointment | `app/Livewire/PatientPortal/BookAppointment.php` | Reserva de citas desde portal paciente |

---

## Middleware (4)

| Middleware | Función |
|---|---|
| `InitializeTenancyBySubdomainId` | Identifica tenant por subdominio (primer segmento del host) |
| `SetTenancyUrlDefaults` | Configura `URL::defaults(['tenant' => ...])`; fallback a `$request->segment(1)` |
| `SyncSpatiePermissionsTeamId` | Sincroniza `setPermissionsTeamId()` con tenant actual + limpia caché |
| `SetLocale` | Detecta y configura idioma (en, es) |

---

## Observers (3)

| Observer | Modelo | Acción |
|---|---|---|
| `OdontogramObserver` | Odontogram | Al cambiar status a `completed` → genera presupuesto automático + notificación toast |
| `AppointmentObserver` | Appointment | Deducción automática de inventario al crear cita |
| `ClinicObserver` | Clinic | Eventos del ciclo de vida del tenant |

---

## Services (2)

| Servicio | Función |
|---|---|
| `BudgetGenerator` | Genera presupuesto desde odontograma completado: agrupa por procedimiento, resuelve precios vía `procedure_price_id` → fallback `diagnosis_code`, 30 días expiración |
| `TenantService` | Lógica de negocio para gestión de tenants |

---

## Commands (4)

| Comando | Función |
|---|---|
| `diagnostic:all` | Diagnóstico completo del sistema (opción `--skip-tests`) |
| `test:routes` | Diagnóstico de rutas Filament |
| `check:schema` | Verifica esquema de base de datos |
| `debug:tenant` | Debug de tenant actual |

---

## Controllers (4)

| Controller | Función |
|---|---|
| `PatientPortalController` | Dashboard, viewBudget, acceptBudget, rejectBudget del portal paciente |
| `ClinicSettingsController` | Guardar configuración de clínica |
| `LegalController` | Páginas `/terms` y `/privacy` |
| `Controller` | Base abstracta |

---

## Traits (3)

| Trait | Descripción |
|---|---|
| `BelongsToClinic` | Global scope `ClinicScope` + auto-set `clinic_id` en `creating` |
| `HasSpatiePermissions` | Gestión de permisos Spatie por tenant |
| `ActivityLogger` | Log de actividades en `SystemActivity` |

---

## Scopes (1)

| Scope | Descripción |
|---|---|
| `ClinicScope` | Filtra `clinic_id = tenant('id')` cuando tenancy está inicializado; macro `withoutTenancy()` para bypass |

---

## Policies (13)

| Policy | Modelo |
|---|---|
| `AppointmentPolicy` | Appointment |
| `BudgetPolicy` | Budget |
| `ClinicPolicy` | Clinic |
| `ClinicalRecordPolicy` | ClinicalRecord |
| `InventoryPolicy` | Inventory |
| `OdontogramPolicy` | Odontogram |
| `PatientPolicy` | Patient |
| `PaymentPolicy` | Payment |
| `ProcedurePricePolicy` | ProcedurePrice |
| `RolePolicy` | Role |
| `SubscriptionPaymentPolicy` | SubscriptionPayment |
| `SystemActivityPolicy` | SystemActivity |
| `UserPolicy` | User |

---

## Mail (3)

| Clase | Plantilla | Trigger |
|---|---|---|
| `BudgetSent` | `emails.budget.sent` | Al enviar presupuesto al paciente |
| `AppointmentReminder` | `emails.appointments.reminder` | Recordatorio de cita |
| `WelcomeClinic` | `emails.welcome-clinic` | Al registrar nueva clínica |

---

## Migraciones (24)

Últimas 4 migraciones (Mayo 2026):

| Migración | Cambio |
|---|---|
| `2026_05_15_183815_add_doctor_id_to_patients_table.php` | Agrega `doctor_id` a `patients` |
| `2026_05_11_152000_update_treatments_table_to_match_seeder.php` | Actualiza tabla `treatments` para seeder |
| `2026_05_11_151000_add_expiration_type_to_inventories.php` | Agrega tipo de expiración a `inventories` |
| `2026_05_11_145636_update_inventories_table_to_match_model.php` | Alinea tabla `inventories` con modelo |

Base (18 migraciones 2024): tenants, users, permissions, domains, procedure_prices, inventories, patients, odontograms, clinical_records, appointments, budgets, treatments, payments, procedure_inventory, subscription_payments, system_activities, cache/jobs, indexes

---

## Seeders (5)

| Seeder | Registros | Notas |
|---|---|---|
| `DatabaseSeeder` | — | Orquestador, llama a TenantSeeder |
| `TenantSeeder` | 2 clínicas + usuarios + pacientes + odontogramas demo | Inicializa `tenancy()->initialize()` antes de seeders |
| `ProcedurePriceSeeder` | 47 procedimientos por clínica | 10 especialidades: general, endodoncia, periodoncia, cirugía, implantes, ortodoncia, prótesis, estética, pediatría, radiología |
| `InventorySeeder` | 95 items por clínica | Categorías: anestesia, restauración, endodoncia, impresión, ortodoncia, bioseguridad, instrumental, farmacia, radiología, blanqueamiento, prótesis, pedodoncia |
| `PermissionSeeder` | Permisos base | Crea permisos `Action:Resource` para todos los recursos |

---

## Factories (5)

| Factory | Modelo |
|---|---|
| `UserFactory` | User |
| `PatientFactory` | Patient |
| `BudgetItemFactory` | BudgetItem (con `clinic_id` automático) |
| `ProcedurePriceFactory` | ProcedurePrice (con `diagnosis_code` y `duration` integer) |
| `InventoryFactory` | Inventory |

---

## Odontograma Interactivo

### Arquitectura
- **SVG interactivo**: 32 dientes (18 superiores + 18 inferiores, numeración 11-48)
- **6 superficies por diente**: top, bottom, left, right, center, root
- **Multi-selección**: Selección múltiple de superficies para tratamientos en lote
- **Procedimientos dinámicos**: El selector lee de `procedure_prices` (CRUD), mostrando nombre + precio
- **`procedure_price_id`**: Cada `ClinicalRecord` guarda referencia al procedimiento exacto
- **40+ colores**: Mapeo en `tooth.blade.php` con fallback gris para códigos sin color
- **Panel flotante**: No bloqueante para edición
- **Historial por sesiones**: Múltiples odontogramas por paciente

### Códigos de Diagnóstico
| Código | Color | Descripción |
|---|---|---|
| `caries` | #ef4444 | Caries |
| `filled` | #3b82f6 | Restauración |
| `endodontic` | #eab308 | Endodoncia |
| `missing` | #1f2937 | Pieza faltante |
| `crown` | #a855f7 | Corona |
| `healthy` | #ffffff | Sano |

40+ códigos adicionales: `prophylaxis`, `sealant`, `fluoride`, `inlay`, `scaling`, `gingivectomy`, `flap_surgery`, `surgical_extraction`, `wisdom_tooth`, `apicoectomy`, `frenectomy`, `implant`, `implant_crown`, `sinus_lift`, `braces_metal`, `braces_aesthetic`, `ortho_adjustment`, `retainer_fixed`, `retainer_removable`, `crown_pfm`, `crown_zirconia`, `bridge`, `partial_denture`, `full_denture`, `denture_rebase`, `whitening`, `veneer_composite`, `veneer_ceramic`, `gingival_contouring`, `ss_crown`, `pulpotomy`, `space_maintainer`, `consultation`, `xray_periapical`, `xray_panoramic`, `cbct`

---

## Presupuesto Automático (BudgetGenerator)

### Flujo
1. Cambio de status del odontograma a `completed` → `OdontogramObserver::updated()`
2. `BudgetGenerator::generate($odontogram)` en transacción DB:
   - Verifica si ya existe presupuesto (`odontogram_id`) → evita duplicados
   - Obtiene `ClinicalRecords` con `treatment_status != 'completed'`
   - Carga en batch `ProcedurePrice` por `procedure_price_id` + `diagnosis_code` (evita N+1)
   - Para cada registro: precio vía `procedure_price_id` → fallback `diagnosis_code` → fallback defaults
   - Agrupa items por procedimiento, concatena números de diente al nombre
   - Crea `Budget` + `BudgetItems` con `expires_at = now() + 30 días`
3. Notificación toast con monto total generado

---

## Multi-Tenancy

### Identificación
| Ruta | Middleware | Identificación |
|---|---|---|
| `/app` (Filament App Panel) | `InitializeTenancyBySubdomainId` | Subdominio: `clinic1.dentalflow.dev` → `clinic1` |
| `/{tenant}/portal/{patient}` | `InitializeTenancyByPath` | Path segment 1 |
| `/admin` (Filament Admin) | Ninguno | Sin tenant (central) |

- Local dev (`localhost`): acceso directo `/app` (tenant vía `clinic_id` del usuario autenticado)
- `tenancy.central_domains` = `['localhost']` por defecto; configurar en producción

### Aislamiento
- `TenancyServiceProvider::boot()`: `BelongsToTenant::$tenantIdColumn = 'clinic_id'`
- `BelongsToClinic` trait: `ClinicScope` global scope en todos los modelos tenant
- `clinic_id` auto-set en `creating` desde `tenant()->getTenantKey()`
- Bypass: `->withoutTenancy()` o `->withoutGlobalScope(ClinicScope::class)` (super admin)

### Cadena de Middleware (App Panel)
```
EncryptCookies → ... → InitializeTenancyBySubdomainId → SetTenancyUrlDefaults → SyncSpatiePermissionsTeamId → Authenticate
```

### Roles y Permisos
- 4 roles: `super-admin`, `admin`, `doctor`, `assistant`
- Permisos granulares: `Action:Resource` (ej: `ViewAny:Patient`, `Create:Budget`)
- `super-admin`: todos los permisos, `clinic_id = null`
- `admin`: mismos permisos que super-admin pero scoped a una clínica
- `doctor`: CRUD en Patient, Appointment, Odontogram, ClinicalRecord, Budget, Payment
- `assistant`: View+Create+Update en Patient, Appointment, Budget

---

## Rutas

### Central (`routes/web.php`)
| Método | Ruta | Destino |
|---|---|---|
| GET | `/` | `welcome` view |
| GET | `/register` | Livewire `RegisterTenant` |
| GET | `/register/success` | `auth.register-success` view |
| GET | `/login` | Redirect `/admin/login` |
| GET | `/terms` | `LegalController@terms` |
| GET | `/privacy` | `LegalController@privacy` |
| GET | `/lang/{locale}` | Cambio de idioma (en, es) |
| GET | `/{tenant?}/portal/{patient}` | Portal dashboard (signed + throttled) |
| GET | `/{tenant?}/portal/{patient}/book` | Livewire `BookAppointment` |
| GET | `/{tenant?}/portal/budgets/{budget}` | Ver presupuesto |
| POST | `/{tenant?}/portal/budgets/{budget}/accept` | Aceptar presupuesto |
| POST | `/{tenant?}/portal/budgets/{budget}/reject` | Rechazar presupuesto |
| POST | `/app/clinic-settings/save` | Guardar config clínica |

### Tenant (`routes/tenant.php`)
- `GET /{tenant}/` → respuesta texto con tenant ID

---

## Testing (22 Feature + 1 Unit = 189 tests)

### Archivos de Test (22 Feature)

| Archivo | Tipo |
|---|---|
| `SecurityTenantIsolationTest` | Aislamiento multi-tenant (9 tests) |
| `OdontogramFunctionalTest` | Funcionalidad odontograma (10 tests) |
| `PatientAndAppointmentsTest` | Pacientes + citas |
| `AuthorizationRbacTest` | RBAC y autorización |
| `AuthorizationPolicyTest` | Polícies de autorización |
| `HttpApiTest` | HTTP/API endpoints (20 tests) |
| `BudgetGeneratorTest` | Generación automática de presupuestos (8 tests) |
| `CalendarWidgetValidationTest` | Validación de calendario (6 tests) |
| `AuthenticationTest` | Autenticación |
| `PatientPortalTest` | Portal del paciente |
| `SystemReadinessTest` | Salud del sistema |
| `DoctorTest` | Funcionalidad rol doctor |
| `AssistantTest` | Funcionalidad rol asistente |
| `SuperAdminTest` | Funcionalidad super admin |
| `AdminClinicTest` | Funcionalidad admin clínica |
| `ValidationTest` | Validaciones |
| `EdgeCasesTest` | Casos borde |
| `ErrorHandlingTest` | Manejo de errores |
| `ExampleTest` | Test de ejemplo (Feature) |
| `Models/PaymentTest` | Tests de modelo Payment |
| `Models/TreatmentTest` | Tests de modelo Treatment |
| `Services/TenantServiceTest` | Tests de TenantService |

### Base TestCase
- `Tests\TestCase` extiende `Illuminate\Foundation\Testing\TestCase`
- `setUpTenants()`: crea `clinic-a` + `clinic-b`, usuarios, roles, permisos, pacientes
- `switchTenant($id)`: `Tenancy::initialize($id)`
- `actingAsDoctor()`, `actingAsAdmin()`, `actingAsAssistant()`, `actingAsSuperAdmin()`
- Factory helpers: `createOdontogram()`, `createClinicalRecord()`, `createBudget()`, `createBudgetWithItems()`, `createAppointment()`, `createPayment()`, `createProcedurePrice()`, `createInventoryItem()`
- `RefreshDatabase` (truncate, no transacciones por PostgreSQL)
- Portal routes requieren `signed` URLs

---

## CI/CD (`.github/workflows/ci.yml`)

3 jobs en paralelo:

1. **tests** (PostgreSQL 15 service container):
   - `composer install` → `npm ci` → `npm run build` → `cp .env.example .env` → `key:generate` → `migrate --force` → `php artisan test`
2. **code-quality** (non-blocking):
   - `vendor/bin/phpstan analyse --error-format=github || true`
   - `vendor/bin/phpstan analyse app --level=5 --error-format=github || true`
3. **security-scan** (non-blocking):
   - `composer audit`

Triggers: push a `main`/`master`/`develop`, PR a `main`/`master`

---

## Assets

### Vite (4 entry points)
- `resources/css/app.css`
- `resources/js/app.js`
- `resources/css/filament/app/theme.css`
- `resources/css/filament/admin/theme.css`

### Vistas Blade (27 archivos)
- `welcome.blade.php` — Landing page
- `auth/register-success.blade.php` — Éxito de registro
- `components/odontogram/tooth.blade.php` — Renderizado SVG de diente individual
- `emails/` — 3 plantillas de email (budget-sent, appointment-reminder, welcome-clinic)
- `filament/` — 10 vistas para páginas y componentes Filament
- `legal/` — Términos y privacidad
- `livewire/` — 4 vistas Livewire (odontogram, odontogram-v2, register-tenant, book-appointment)
- `patient-portal/` — 2 vistas (dashboard, budget-detail)

---

## Seguridad

### Vulnerabilidades corregidas (Abril 2026)
- 6 vulnerabilidades (3 críticas, 2 altas, 1 media): IDOR, Authorization Bypass, Missing Tenant Scope, Portal sin middleware, Soft Deletes sin verificación — Ver `SECURITY_AUDIT.md`

### Hardening activo
- Rate limiting portal: 30 req/min por IP
- `.env.testing` gitignored
- `require-dev` aislado en `composer.json`
- CI security scan (`composer audit`)
- Signed URLs para rutas de portal

---

## Variables de Entorno Clave

```env
DB_CONNECTION=pgsql          # Obligatorio (default .env.example es sqlite)
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=dentalflowsaas
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña

TENANCY_CENTRAL_DOMAINS=localhost  # Cambiar en producción
```
