# DentalFlow SaaS

> **Sistema de Gestión Dental Multi-Tenant con Odontograma Interactivo**

DentalFlow SaaS es una plataforma completa de gestión para clínicas dentales que permite administrar pacientes, citas, presupuestos y un odontograma interactivo avanzado con historial clínico por sesiones.

![Laravel](https://img.shields.io/badge/Laravel-12.47-red?logo=laravel)
![Filament](https://img.shields.io/badge/Filament-4.x-orange?logo=filament)
![Livewire](https://img.shields.io/badge/Livewire-3.x-pink?logo=livewire)
![PHP](https://img.shields.io/badge/PHP-8.3-blue?logo=php)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-14-blue?logo=postgresql)
![Tests](https://img.shields.io/badge/Tests-175%20passing%20(359%20assertions)-green)

---

## 📋 Tabla de Contenidos

- [Características](#-características)
- [Arquitectura del Sistema](#-arquitectura-del-sistema)
- [Seguridad](#-seguridad)
- [Testing](#-testing)
- [Diagnóstico](#-diagnóstico)
- [Requisitos Previos](#-requisitos-previos)
- [Instalación](#-instalación)
- [Comandos Útiles](#-comandos-útiles)
- [Estructura de Archivos](#-estructura-de-archivos)

---

## ✨ Características

### 🏥 Gestión Multi-Tenant
- Sistema multi-clínica con aislamiento completo de datos
- Dominios personalizados por tenant
- Gestión centralizada desde panel de administración
- Self-onboarding para nuevas clínicas

### 👥 Gestión de Pacientes
- Registro completo de pacientes
- Historial médico y alergias
- Documentos y notas clínicas
- Portal de paciente para reservas

### 📅 Sistema de Citas
- Calendario interactivo con drag-and-drop
- Validación automática de fechas pasadas y solapamientos
- Gestión de horarios personalizados por clínica
- Generación automática de slots
- Notificaciones automáticas

### 💰 Presupuestos
- **Generación automática** desde odontogramas completados
- Creación manual de presupuestos detallados
- Ítems de tratamiento personalizables
- Seguimiento de estados (draft/sent/accepted/rejected)
- Integración con pagos
- Notas y link al odontograma origen

### 🦷 Odontograma Interactivo
- **SVG interactivo** con 32 dientes
- **6 superficies por diente**: top, bottom, left, right, center, root
- **Multi-selección** de superficies
- **Procedimientos dinámicos** desde CRUD (47+ procedimientos reales)
- **Historial por sesiones** - múltiples odontogramas
- **40+ colores** para todos los tipos de procedimiento
- **Panel flotante** no bloqueante
- **Presupuesto automático** con notificación al completar odontograma

### 📊 Business Intelligence
- Dashboard con métricas financieras
- Gráficos de ingresos
- Estadísticas de aceptación de presupuestos
- Widgets de Filament

---

## 🏗️ Arquitectura del Sistema

### Stack Tecnológico
```
Backend:     Laravel 12.x, PHP 8.2+
Frontend:    Filament 4.x, Livewire 3.x
Database:    PostgreSQL 14+
Multi-tenancy: Stancl Tenancy 3.9
Auth/RBAC:   Spatie Permissions 6.0
```

### Diagrama de Middleware
```
Request → InitializeTenancyBySubdomainId → SetTenancyUrlDefaults → SyncSpatiePermissionsTeamId → App
```

### Aislamiento Multi-Tenant
- Cada clínica tiene su propio `clinic_id`
- Global scopes filtran automáticamente por tenant
- Consultas blindadas contra fugas de datos
- Permisos sincronizados con clinic_id

---

## 🔒 Seguridad

### Vulnerabilidades Corregidas (2026-04-21)

| # | Vulnerabilidad | Severidad | Archivo | Estado |
|---|-------------|----------|---------|--------|
| 1 | IDOR Patient Portal Dashboard | 🔴 CRÍTICA | PatientPortalController.php | ✅ |
| 2 | IDOR Budget Acceptance | 🔴 CRÍTICA | PatientPortalController.php | ✅ |
| 3 | Authorization Bypass | 🔴 CRÍTICA | OdontogramsRelationManager.php | ✅ |
| 4 | Missing Tenant Scope | 🟠 ALTA | Odontogram.php | ✅ |
| 5 | Portal Sin Middleware | 🟠 ALTA | routes/web.php | ✅ |
| 6 | Soft Deletes Sin Verificación | 🟡 MEDIA | OdontogramsRelationManager.php | ✅ |

### Hardening Adicional
- Rate limiting en portal (30 req/min por IP)
- Credenciales de test en `.env.testing` (gitignored)
- `require-dev` aislado en `composer.json`

### RBAC
- Roles por clínica: Doctor, Asistente, Admin
- Permisos granulares por recurso
- Sincronización de permisos con clinic_id

---

## 🧪 Testing

### Suite de Tests (175 tests, 359 aserciones)

```bash
# Ejecutar todos los tests
php artisan test

# Tests específicos
php artisan test --filter=SecurityTenantIsolationTest
php artisan test --filter=OdontogramFunctionalTest
php artisan test --filter=PatientAndAppointmentsTest
php artisan test --filter=AuthorizationRbacTest
php artisan test --filter=BudgetGeneratorTest
php artisan test --filter=CalendarWidgetValidationTest
php artisan test --filter=HttpApiTest
```

### Tests de Aislamiento (9 tests)
- Patient isolation by clinic ✅
- Cannot access patient from other clinic ✅
- Odontogram isolation by clinic ✅
- Clinical record isolation by clinic ✅
- Budget isolation by clinic ✅
- User belongs to correct clinic ✅
- Tenant context isolation ✅
- Global scopes isolate queries ✅

### Tests de Funcionalidad (10 tests)
- Create odontogram session ✅
- Add clinical record ✅
- Multiple records ✅
- All 32 teeth ✅
- Multiple sessions ✅
- Filter by diagnosis ✅
- Valid codes ✅
- Valid surfaces ✅
- Treatment status ✅

### Tests HTTP/API (20 tests)
- Landing page ✅
- Auth routes ✅
- Admin panel ✅
- Clinic panel ✅
- Patient portal ✅
- API endpoints ✅
- Health check `/up` ✅

### Tests de Presupuesto Automático (7 tests)
- Generación desde odontograma completado ✅
- No duplica presupuestos existentes ✅
- Manejo de registros vacíos ✅
- Mapeo con ProcedurePrice por diagnosis_code ✅
- Omite tratamientos completados ✅
- Fecha de expiración automática ✅
- Notas de generación automática ✅

### Tests de Validación de Calendario (6 tests)
- No permite reprogramar a fechas pasadas ✅
- No permite solapamientos ✅
- Permite slots válidos futuros ✅
- Manejo de citas inexistentes ✅
- Omite citas canceladas ✅
- Eventos en rango de fechas ✅

---

## 🔬 Diagnóstico

### Comando Unificado
```bash
php artisan diagnostic:all           # Completo con tests
php artisan diagnostic:all --skip-tests  # Solo diagnóstico
```

### Scripts de Verificación
```bash
php verify_system_health.php    # Salud del sistema
php verify_all_phases.php     # Features por fases
php verify_registration.php  # Registro de clínicas
php benchmark.php           # Rendimiento
```

### Comandos de Rutas
```bash
php artisan test:routes     # Diagnosticar rutas Filament
php artisan route:list     # Listar todas las rutas
```

### Benchmark
```bash
php benchmark.php
```
**Resultado promedio: 35-40ms** 🚀

---

## 📦 Requisitos Previos

- PHP >= 8.2
- Composer >= 2.0
- Node.js >= 18.x
- PostgreSQL >= 14
- Git

---

## 🚀 Instalación

```bash
# Clonar
git clone https://github.com/rommelescorihuela/dentalflowsaas.git
cd dentalflowsaas

# Instalar dependencias
composer install
npm install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Migraciones
php artisan migrate

# Seed de procedimientos por defecto
php artisan db:seed --class=ProcedurePriceSeeder

# Crear usuario admin
php artisan make:filament-user

# Compilar assets
npm run dev
```

---

## ⚙️ Comandos Útiles

```bash
# Diagnóstico
php artisan diagnostic:all           # Diagnóstico completo
php artisan test:routes             # Verificar rutas

# Tenancy
php artisan tenants:create           # Crear tenant
php artisan tenants:migrate         # Migrar tenant

# Tests
php artisan test                    # Todos los tests
php artisan test --filter=TestName    # Test específico

# Seeders
php artisan db:seed --class=ProcedurePriceSeeder  # 47 procedimientos reales
php artisan db:seed --class=InventorySeeder        # 95 items de inventario real

# Limpieza
php artisan optimize:clear           # Limpiar caché
```

---

## 📂 Estructura de Archivos

```
dentalflowsaas/
├── app/
│   ├── Console/Commands/
│   │   ├── SystemDiagnosticCommand.php  # Diagnóstico unificado
│   │   └── TestRoutesCommand.php     # Diagnosticar rutas
│   ├── Filament/App/               # Panel de clínica
│   │   ├── Resources/
│   │   │   ├── Patients/
│   │   │   │   ├── PatientResource.php
│   │   │   │   └── RelationManagers/
│   │   │   │       └── OdontogramsRelationManager.php  # Botón "Generate Budget"
│   │   │   └── Budgets/
│   │   │       └── BudgetResource.php  # Link a odontograma, notas, colores
│   │   └── Widgets/
│   │       └── CalendarWidget.php  # Validación drag-and-drop
│   ├── Http/Middleware/
│   │   ├── SyncSpatiePermissionsTeamId.php
│   │   └── SetTenancyUrlDefaults.php
│   ├── Livewire/
│   │   ├── Odontogram.php            # Odontograma interactivo
│   │   └── PatientPortal/
│   │       └── BookAppointment.php   # Duración dinámica
│   ├── Models/
│   │   ├── Patient.php
│   │   ├── Odontogram.php            # ActivityLogger añadido
│   │   ├── ClinicalRecord.php
│   │   ├── Budget.php                # odontogram_id, notes
│   │   └── BudgetItem.php            # BelongsToClinic añadido
│   ├── Observers/
│   │   ├── AppointmentObserver.php   # Deducción de inventario
│   │   └── OdontogramObserver.php    # Generación automática de presupuestos
│   ├── Services/
│   │   └── BudgetGenerator.php       # Servicio de generación de presupuestos
│   └── Traits/
│       ├── BelongsToClinic.php
│       ├── HasSpatiePermissions.php
│       └── ActivityLogger.php
├── tests/Feature/
│   ├── SecurityTenantIsolationTest.php
│   ├── OdontogramFunctionalTest.php
│   ├── PatientAndAppointmentsTest.php
│   ├── AuthorizationRbacTest.php
│   ├── SystemReadinessTest.php
│   ├── HttpApiTest.php
│   ├── BudgetGeneratorTest.php       # 7 tests
│   └── CalendarWidgetValidationTest.php  # 6 tests
├── database/
│   ├── factories/
│   │   ├── BudgetItemFactory.php     # Nueva factory
│   │   └── ProcedurePriceFactory.php # Con diagnosis_code
│   ├── migrations/
│   │   ├── ..._add_clinic_id_to_budget_items_table.php
│   │   ├── ..._add_odontogram_id_to_budgets_table.php
│   │   ├── ..._add_notes_to_budgets_table.php
│   │   ├── ..._add_diagnosis_code_to_procedure_prices_table.php
│   │   └── ..._add_procedure_price_id_to_clinical_records_table.php
│   └── seeders/
│       ├── ProcedurePriceSeeder.php  # 47 procedimientos reales
│       ├── InventorySeeder.php       # 95 items de inventario real
│       └── TenantSeeder.php          # Datos demo con odontogramas
├── .github/workflows/ci.yml          # CI/CD pipeline
├── Dockerfile                        # Producción PHP 8.3-fpm
├── DEPLOY.md                         # Guía de despliegue
├── SECURITY_AUDIT.md                 # Informe de seguridad
├── CONTEXT.md                        # Contexto del proyecto
└── README.md                         # Este archivo
```

---

## 🗄️ Estructura de Base de Datos

### Tablas Principales
| Tabla | Descripción |
|-------|------------|
| `tenants` | Clínicas (multi-tenant) |
| `users` | Usuarios con roles |
| `patients` | Pacientes |
| `odontograms` | Sesiones de odontograma |
| `clinical_records` | Registros por superficie |
| `appointments` | Citas |
| `budgets` | Presupuestos (con odontogram_id, notes) |
| `budget_items` | Items de presupuesto (con clinic_id) |
| `procedure_prices` | Precios de procedimientos (con diagnosis_code) |
| `clinical_records` | Registros por superficie (con procedure_price_id) |
| `payments` | Pagos |
| `system_activities` | Log de actividades |
| `inventories` | Inventario (95 items reales) |
| `procedure_inventory` | Inventario de procedimientos |

### Diagnósticos del Odontograma
| Código | Color | Descripción |
|--------|-------|-------------|
| `caries` | 🔴 #ef4444 | Caries |
| `filled` | 🔵 #3b82f6 | Restauración |
| `endodontic` | 🟡 #eab308 | Endodóntico |
| `missing` | ⚫ #1f2937 | Faltante |
| `crown` | 🟣 #a855f7 | Corona |
| `healthy` | ⚪ #ffffff | Sano |

> **Nota**: El odontograma ahora lee procedimientos dinámicamente desde el CRUD (`procedure_prices`). Se soportan 40+ códigos adicionales (implantes, ortodoncia, prótesis, etc.) con colores mapeados automáticamente.

---

## 📊 Diagnóstico Actual (2026-04-29)

### Estado del Sistema
```
✅ Base de datos: OK
✅ Clínicas: 2 activas (clinic1, clinic2)
✅ Usuarios: Dr. House (clinic1), Dr. Strange (clinic2)
✅ Onboarding: OK
✅ Patient Portal: 18 slots
✅ BI Dashboard: 3 KPIs
✅ Tenant Isolation: OK
✅ Odontogram: OK (procedimientos dinámicos desde CRUD)
✅ Presupuesto automático: OK (con notificación toast)
✅ Rate limiting portal: OK
✅ Permisos Odontogram: OK (7 permisos CRUD)
```

### Datos Demo
```
✅ Procedimientos: 47 por clínica (catálogo completo)
✅ Inventario: 95 items por clínica (datos reales)
✅ Odontogramas: 5 con registros demo (clinic1)
✅ Registros Clínicos: 40 demo (caries, restauraciones, endodoncias, coronas)
```

### Benchmark
```
Landing:       40ms  ✅
Register:     35ms  ✅
Admin Login:   47ms  ✅
Health:       32ms  ✅
--------------------------------
Promedio:     38ms  🚀
```

### Tests
```
Tests: 175 passed, 359 assertions ✅
Duration: ~35s
```

### Mejoras Recientes
- **Odontograma dinámico**: Procedimientos desde CRUD en lugar de opciones hardcodeadas
- **47 procedimientos reales**: Catálogo completo por especialidad
- **95 items de inventario real**: Datos con proveedores y precios reales
- **Migración `procedure_price_id`**: Vinculación directa a procedimientos
- **40+ colores mapeados**: Soporte para todos los tipos de procedimiento
- **Presupuesto automático mejorado**: Notificación toast con monto generado
- **Permisos Odontogram**: 7 permisos CRUD agregados
- Validación de fechas pasadas y solapamientos en citas
- RUT único por clínica
- Credenciales de test seguras (`.env.testing` en `.gitignore`)
- 34 tests redundantes eliminados
- 39 aserciones débiles reemplazadas por fuertes
- 20 nuevos tests HTTP/API
- 7 tests de generación automática de presupuestos
- 6 tests de validación de calendario
- Health check `/up` configurado
- `require-dev` correctamente aislado
- CI/CD pipeline configurado
- Dockerfile y guía de despliegue

### Preparación para Producción
- Desplegar con `composer install --no-dev --optimize-autoloader`
- Usar `npm install && npm run build` para assets
- Configurar `COMPOSER_FLAGS=--no-dev` en CI/CD o Forge/Vapor
- CI/CD: `.github/workflows/ci.yml` (tests, code quality, security scan)
- Docker: `Dockerfile` (PHP 8.3-fpm, production-ready)
- Guía completa: `DEPLOY.md`
- **Emails transaccionales**: Presupuesto enviado, recordatorio de citas, reset de contraseña
- **Legal**: Términos de Servicio (`/terms`) y Política de Privacidad (`/privacy`)

---

## 📝 Licencia

Proyecto privado para clínicas dentales autorizadas.

---

**Desarrollado con ❤️ para la comunidad dental**
