# DentalFlow SaaS - Informe de Vulnerabilidades de Seguridad

> Análisis realizado: 21 de Abril de 2026
> Última actualización: 28 de Abril de 2026
> Sistema: DentalFlow SaaS Multi-Tenant
> Severidad: 🔴 CRÍTICA | 🟠 ALTA | 🟡 MEDIA

---

## Resumen Ejecutivo

Se identificaron **6 vulnerabilidades** que pueden resultar en:
- Acceso no autorizado a datos de pacientes de otras clínicas (IDOR)
- Cruce de información entre tenants
- Pérdida potencial de datos clínicos

---

## VULNERABILIDAD #1: IDOR en Patient Portal Dashboard 🔴 CRÍTICA

### Ubicación
- **Archivo**: `routes/web.php:34`
- **Controlador**: `app/Http/Controllers/PatientPortalController.php:13-22`

### Descripción
El parámetro `$patient` en la ruta `/portal/{patient}` se usa directamente para buscar un paciente SIN verificar que pertenece a la clínica del tenant actual o del paciente autenticado.

### Solución Aplicada
```php
// Ahora verifica que el paciente pertenece al tenant actual
$patient = Patient::where('id', $patient)
    ->where('clinic_id', tenant('id'))
    ->firstOrFail();
```

---

## VULNERABILIDAD #2: IDOR en Budget Acceptance 🔴 CRÍTICA

### Ubicación
- **Archivo**: `routes/web.php:36`
- **Controlador**: `app/Http/Controllers/PatientPortalController.php:25-32`

### Descripción
La ruta para aceptar presupuestos NO verifica que el presupuesto pertenece al paciente que está intentando aceptarlo.

### Solución Aplicada
```php
// Verifica que el presupuesto pertenece al tenant actual
if ($budget->clinic_id !== tenant('id')) {
    abort(403);
}
```

---

## VULNERABILIDAD #3: Authorization Bypass en RelationManager 🔴 CRÍTICA

### Ubicación
- **Archivo**: `app/Filament/App/Resources/Patients/RelationManagers/OdontogramsRelationManager.php:29-32`

### Descripción
El método `shouldSkipAuthorization()` retornaba `true` permanentemente, deshabilitando TODA verificación de autorización.

### Solución Aplicada
```php
public static function shouldSkipAuthorization(): bool
{
    return false; // Usar autorización normal
}

// Métodos de autorización específicos implementados
public function canCreate(): bool { ... }
public function canEdit($record): bool { ... }
public function canDelete($record): bool { ... }
public function canView($record): bool { ... }
```

---

## VULNERABILIDAD #4: Missing Tenant Scope en Odontogram Queries 🟠 ALTA

### Ubicación
- **Archivo**: `app/Livewire/Odontogram.php:107-110`

### Descripción
Al cargar datos existentes para populación del formulario, no se filtraba por `clinic_id`.

### Solución Aplicada
```php
// Agregado filtro clinic_id en queries
$existing = ClinicalRecord::where('clinic_id', tenant('id'))
    ->where('patient_id', $this->record->id)
    ...
```

---

## VULNERABILIDAD #5: Portal Routes Sin Middleware de Tenancy 🟠 ALTA

### Ubicación
- **Archivo**: `routes/web.php:33-36`

### Descripción
Las rutas del patient portal solo tenían middleware `web` y `signed`, NO incluían middleware de tenancy.

### Solución Aplicada
```php
Route::middleware([
    'web',
    'signed',
    \Stancl\Tenancy\Middleware\InitializeTenancyByPath::class,
])->group(function () { ... });
```

---

## VULNERABILIDAD #6: Soft Deletes Sin Verificación de Clínica 🟡 MEDIA

### Ubicación
- **Archivo**: `app/Filament/App/Resources/Patients/RelationManagers/OdontogramsRelationManager.php:160-171`

### Descripción
Al eliminar odontogramas, no se verificaba explícitamente que el registro pertenece a la clínica actual.

### Solución Aplicada
```php
// Verificación de clínica antes de eliminar
if ($record->clinic_id !== tenant('id')) {
    abort(403);
}
$record->delete();
```

---

## Matriz de Riesgo

| # | Vulnerabilidad | Severidad | Tipo | Impacto | Estado |
|---|---------------|-----------|------|--------|--------|
| 1 | IDOR en Patient Portal Dashboard | 🔴 CRÍTICA | IDOR | Acceso a historial de otras clínicas | ✅ CORREGIDO |
| 2 | IDOR en Budget Acceptance | 🔴 CRÍTICA | IDOR | Manipulación de presupuestos | ✅ CORREGIDO |
| 3 | Authorization Bypass (RelationManager) | 🔴 CRÍTICA | Auth Bypass | Acceso sin restricciones | ✅ CORREGIDO |
| 4 | Missing Tenant Scope (Odontogram) | 🟠 ALTA | Data Leak | Lectura de registros | ✅ CORREGIDO |
| 5 | Portal Routes Sin Tenancy Middleware | 🟠 ALTA | Config | Contexto no verificado | ✅ CORREGIDO |
| 6 | Soft Deletes Sin Verificación | 🟡 MEDIA | Data Loss | Posible eliminación indebida | ✅ CORREGIDO |

---

## Mejoras de Seguridad y Hardening (2026-04-28)

### Credenciales Seguras
- `.env.testing` creado con credenciales de prueba
- Añadido a `.gitignore` para prevenir exposición accidental
- `phpunit.xml` limpio de credenciales hardcodeadas

### Validaciones de Modelo
- `Appointment.php`: validación de fechas pasadas
- `Appointment.php`: validación de solapamiento de horarios
- `Patient.php`: RUT único por clínica (migración `2026_04_27_195120`)
- `CalendarWidget.php`: validación en drag-and-drop (fechas pasadas, solapamientos)

### Rate Limiting
- Portal routes: 30 req/min por IP
- Previene abuso/spam en reservas de pacientes

### Testing de Seguridad
- 20 nuevos tests HTTP/API para verificar rutas protegidas
- 34 tests redundantes eliminados
- 39 aserciones débiles reemplazadas por fuertes (`assertEquals`, `assertCount`)
- Health check `/up` configurado en `bootstrap/app.php`
- CI/CD pipeline con security scan (`composer audit`)

### Aislamiento Tenant Mejorado
- `BudgetItem` ahora tiene `BelongsToClinic` trait (antes bypass)
- `ActivityLogger` prioriza `tenant('id')` sobre `session('tenant_id')`
- Rutas portal centralizadas en `web.php` (eliminadas duplicadas de `tenant.php`)

### Preparación para Producción
- `require-dev` correctamente aislado en `composer.json`
- Desplegar con `composer install --no-dev --optimize-autoloader`
- Configurar `COMPOSER_FLAGS=--no-dev` en CI/CD o Forge/Vapor
- CI/CD: `.github/workflows/ci.yml` (tests, code quality, security scan)
- Docker: `Dockerfile` (PHP 8.3-fpm, production-ready)
- Guía: `DEPLOY.md` (manual, Docker Compose, Forge/Vapor, Nginx, rollback)
- **Emails transaccionales**: Presupuesto enviado, recordatorio de citas, reset de contraseña
- **Legal**: Términos de Servicio (`/terms`) y Política de Privacidad (`/privacy`)

---

## Verificación Post-Fijo

```bash
# Verificar que authorization no está saltada
grep -r "shouldSkipAuthorization.*return true" app/

# Verificar que todas las queries tienen clinic_id
grep -r "::where(" app/Livewire/Odontogram.php

# Verificar sintaxis PHP
php -l app/Http/Controllers/PatientPortalController.php
php -l app/Filament/App/Resources/Patients/RelationManagers/OdontogramsRelationManager.php
php -l app/Livewire/Odontogram.php
php -l routes/web.php

# Ejecutar tests completos
php artisan test

# Verificar rate limiting
php artisan route:list --path=portal
```
