# DentalFlow SaaS

> **Sistema de Gestión Dental Multi-Tenant con Odontograma Interactivo**

DentalFlow SaaS es una plataforma completa de gestión para clínicas dentales que permite administrar pacientes, citas, presupuestos y un odontograma interactivo avanzado con historial clínico por sesiones.

![Laravel](https://img.shields.io/badge/Laravel-12.47-red?logo=laravel)
![Filament](https://img.shields.io/badge/Filament-3.x-orange?logo=filament)
![Livewire](https://img.shields.io/badge/Livewire-3.x-pink?logo=livewire)
![PHP](https://img.shields.io/badge/PHP-8.3-blue?logo=php)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-14-blue?logo=postgresql)
![Tests](https://img.shields.io/badge/Tests-180%20API%20%2B%2011%20E2E-green)

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
- Calendario interactivo
- Gestión de horarios personalizados por clínica
- Generación automática de slots
- Notificaciones automáticas

### 💰 Presupuestos
- Creación de presupuestos detallados
- Ítems de tratamiento personalizables
- Seguimiento de estados (pending/accepted/completed)
- Integración con pagos

### 🦷 Odontograma Interactivo
- **SVG interactivo** con 32 dientes
- **6 superficies por diente**: top, bottom, left, right, center, root
- **Multi-selección** de superficies
- **Historial por sesiones** - múltiples odontogramas
- **Códigos de diagnóstico** con colores
- **Panel flotante** no bloqueante

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
Request → InitializeTenancyByDomain → SetTenancyUrlDefaults → SyncSpatiePermissionsTeamId → ForceOnboardingMiddleware → App
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

### RBAC
- Roles por clínica: Doctor, Asistente, Admin
- Permisos granulares por recurso
- Sincronización de permisos con clinic_id

---

## 🧪 Testing

### Suite de Tests (48 tests, 88 aserciones)

```bash
# Ejecutar todos los tests
php artisan test

# Tests específicos
php artisan test --filter=SecurityTenantIsolationTest
php artisan test --filter=OdontogramFunctionalTest
php artisan test --filter=PatientAndAppointmentsTest
php artisan test --filter=AuthorizationRbacTest
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
│   │   │   └── Budgets/
│   │   └── Widgets/
│   ├── Http/Middleware/
│   │   ├── SyncSpatiePermissionsTeamId.php
│   │   ├── ForceOnboardingMiddleware.php
│   │   └── SetTenancyUrlDefaults.php
│   ├── Livewire/
│   │   ├── Odontogram.php            # Odontograma interactivo
│   │   └── PatientPortal/
│   │       └── BookAppointment.php
│   ├── Models/
│   │   ├── Patient.php
│   │   ├── Odontogram.php
│   │   ├── ClinicalRecord.php
│   │   └── Budget.php
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
│   └── ExampleTest.php
├── database/migrations/
├── benchmark.php                    # Script de rendimiento
├── verify_system_health.php      # Verificación sistema
├── verify_all_phases.php         # Verificación features
├── verify_registration.php        # Verificación registro
├── SECURITY_AUDIT.md            # Informe de seguridad
├── CONTEXT.md                   # Contexto del proyecto
└── README.md                    # Este archivo
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
| `budgets` | Presupuestos |
| `budget_items` | Items de presupuesto |
| `payments` | Pagos |
| `system_activities` | Log de actividades |

### Diagnósticos del Odontograma
| Código | Color | Descripción |
|--------|-------|-------------|
| `caries` | 🔴 #ef4444 | Caries |
| `filled` | 🔵 #3b82f6 | Restauración |
| `endodontic` | 🟡 #eab308 | Endodóntico |
| `missing` | ⚫ #1f2937 | Faltante |
| `crown` | 🟣 #a855f7 | Corona |
| `healthy` | ⚪ #ffffff | Sano |

---

## 📊 Diagnóstico Actual (2026-04-21)

### Estado del Sistema
```
✅ Base de datos: OK
✅ Clínicas: 8 activas
✅ Usuarios: 9 registrados
✅ Onboarding: OK
✅ Patient Portal: 18 slots
✅ BI Dashboard: 3 KPIs
✅ Tenant Isolation: OK
✅ Odontogram: OK
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
Tests: 48 passed, 88 assertions ✅
Duration: ~11s
```

---

## 📝 Licencia

Proyecto privado para clínicas dentales autorizadas.

---

**Desarrollado con ❤️ para la comunidad dental**