# Changelog

Todas las modificaciones notables de DentalFlow SaaS documentadas aquí.

---

## [2026-06] — Mejoras Recientes

### Traducción al Español
- Traducción completa de todos los recursos Filament (Admin y App) al español
- Labels de navegación, modelos, formularios y tablas en español
- Corrección de tipos `BackedEnum`/`UnitEnum` para compatibilidad con Filament 4

### Portal de Pacientes Mejorado
- Rediseño del wizard de reserva de citas (`BookAppointment`)
- Mejoras en dashboard y visualización de presupuestos del portal

### Filtros en Recursos Filament
- Filtros avanzados agregados a todos los recursos del panel de clínica
- Búsqueda y ordenamiento mejorados en tablas

### UI/UX
- Tema unificado con tipografía Instrument Sans
- Dashboard rediseñado con KPIs financieros y widgets de crecimiento
- Mejoras visuales en ambas interfaces (Admin y App)

---

## [2026-05] — Asignación de Doctores y Acceso

### Asignación Doctor-Paciente
- Nueva columna `doctor_id` en tabla `patients`
- Migración: `2026_05_15_183815_add_doctor_id_to_patients_table.php`

### Polícies de Acceso Basadas en Roles
- 13 policies creadas para control de acceso granular
- Restricciones: doctor solo ve sus pacientes, assistant acceso limitado
- `PatientPolicy`, `AppointmentPolicy`, `BudgetPolicy`, etc.

### Actualización de Tratamientos e Inventario
- Migración `2026_05_11_152000`: alinea tabla `treatments` con seeder
- Migración `2026_05_11_151000`: agrega `expiration_type` a `inventories`
- Migración `2026_05_11_145636`: alinea `inventories` con modelo

---

## [2026-04] — Seguridad y Multi-Tenancy

### Correcciones de Seguridad (ver `SECURITY_AUDIT.md`)
- **6 vulnerabilidades críticas corregidas**: 3 IDOR, 1 Auth Bypass, 1 Missing Tenant Scope, 1 Portal sin Middleware
- Hardening: rate limiting en portal (30 req/min), `.env.testing` gitignored
- Mejora de aislamiento: `BudgetItem` ahora tiene `BelongsToClinic`

### Multi-Tenancy por Subdominio
- Implementación de `InitializeTenancyBySubdomainId` middleware
- Identificación de tenant vía subdominio: `clinic1.dentalflow.dev` → `clinic1`
- Configuración dinámica de dominios centrales vía `config('tenancy.central_domains')`
- Página `ClinicSettings` para gestión de configuración por clínica

### Odontograma Dinámico
- Procedimientos leídos desde CRUD `procedure_prices` (ya no hardcodeados)
- Nueva columna `procedure_price_id` en `clinical_records` para vinculación directa
- 40+ colores mapeados automáticamente para todos los tipos de procedimiento
- Fallback seguro en `tooth.blade.php` para códigos sin color

### Seeders con Datos Reales
- `ProcedurePriceSeeder`: 47 procedimientos organizados por especialidad
- `InventorySeeder`: 95 items de inventario con datos reales
- `TenantSeeder`: datos demo con odontogramas y registros clínicos

### Presupuesto Automático
- `BudgetGenerator` service: genera presupuestos desde odontogramas completados
- `OdontogramObserver`: notificación toast con monto generado
- Botón "Generate Budget" manual en `OdontogramsRelationManager`
- `Budget` con `odontogram_id`, `notes`, `expires_at` (30 días)

### Sistema de Actividades
- `ActivityLogger` trait agregado a `Odontogram` y `Treatment`
- `SystemActivity` model: log de acciones del sistema
- `SystemDiagnosticCommand`: diagnóstico unificado `php artisan diagnostic:all`

### Testing
- 189 tests (22 archivos Feature + 1 Unit)
- `SecurityTenantIsolationTest`: 9 tests de aislamiento
- `BudgetGeneratorTest`: 8 tests de generación automática
- `CalendarWidgetValidationTest`: 6 tests de validación
- `HttpApiTest`: 20 tests de endpoints HTTP/API
- Eliminados 34 tests redundantes, reemplazadas 39 aserciones débiles

### Validaciones
- `Appointment`: validación de fechas pasadas
- `Appointment`: validación de solapamiento de horarios
- `CalendarWidget`: validación en drag-and-drop
- `Patient`: RUT único por clínica

---

## [2026-03] — Base del Sistema

### Fundación Multi-Tenant
- Stancl Tenancy 3.9 con shared-DB approach (`clinic_id`)
- `BelongsToClinic` trait + `ClinicScope` global scope
- `TenancyServiceProvider`: configuración de columna tenant y eventos

### Paneles Filament
- `AdminPanelProvider` (panel `admin`, path `/admin`): gestión central
- `AppPanelProvider` (panel `app`, path `/app`): panel de clínica

### Modelos Base
- 18 modelos con soporte multi-tenant: Patient, Odontogram, ClinicalRecord, Budget, BudgetItem, Appointment, Treatment, Payment, ProcedurePrice, Inventory, ProcedureInventory, Clinic, Domain, User, Role, Permission, SystemActivity, SubscriptionPayment

### Infraestructura
- CI/CD con GitHub Actions (tests, PHPStan, security audit)
- Dockerfile PHP 8.3-fpm para producción
- Vite 7 con Tailwind 4 para assets
- Nginx con soporte wildcard subdomain

---

## [2026-02] — Setup Inicial

- Laravel 12 base con Filament 4
- Stancl Tenancy para multi-tenancy
- Spatie Permissions para RBAC
- Laravel Cashier para Stripe
- Migraciones iniciales: tenants, users, permissions, domains, procedure_prices, inventories, patients, odontograms, clinical_records, appointments, budgets, treatments, payments, procedure_inventory, subscription_payments, system_activities
