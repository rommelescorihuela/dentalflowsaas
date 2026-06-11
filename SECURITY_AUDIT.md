# DentalFlow SaaS — Informe de Vulnerabilidades de Seguridad

> Análisis realizado: 21 de Abril de 2026
> Última actualización: 28 de Abril de 2026
> Estado actual: **Todas las vulnerabilidades corregidas** ✅
> Sistema: DentalFlow SaaS Multi-Tenant
> Severidad: 🔴 CRÍTICA | 🟠 ALTA | 🟡 MEDIA

---

## Resumen Ejecutivo

Se identificaron y corrigieron **6 vulnerabilidades** que podían resultar en:
- Acceso no autorizado a datos de pacientes de otras clínicas (IDOR)
- Cruce de información entre tenants
- Pérdida potencial de datos clínicos

---

## VULNERABILIDAD #1: IDOR en Patient Portal Dashboard 🔴 CRÍTICA

**Ubicación**: `routes/web.php:56` → `PatientPortalController.php:13-22`

El parámetro `$patient` en la ruta `/portal/{patient}` se usaba directamente para buscar un paciente sin verificar que pertenece al tenant actual.

**Solución**: Verificación `Patient::where('id', $patient)->where('clinic_id', tenant('id'))->firstOrFail()`

---

## VULNERABILIDAD #2: IDOR en Budget Acceptance 🔴 CRÍTICA

**Ubicación**: `routes/web.php:59-60` → `PatientPortalController.php:25-32`

La ruta para aceptar presupuestos no verificaba que el presupuesto pertenece al paciente que lo intenta aceptar.

**Solución**: Validación `$budget->clinic_id !== tenant('id')` → abort 403

---

## VULNERABILIDAD #3: Authorization Bypass en RelationManager 🔴 CRÍTICA

**Ubicación**: `OdontogramsRelationManager.php:29-32`

El método `shouldSkipAuthorization()` retornaba `true` permanentemente, deshabilitando toda verificación de autorización.

**Solución**: Cambiado a `return false` + métodos de autorización específicos (`canCreate`, `canEdit`, `canDelete`, `canView`)

---

## VULNERABILIDAD #4: Missing Tenant Scope en Odontogram Queries 🟠 ALTA

**Ubicación**: `app/Livewire/Odontogram.php:107-110`

Al cargar datos existentes para populación del formulario, no se filtraba por `clinic_id`.

**Solución**: Agregado filtro `->where('clinic_id', tenant('id'))` en queries de `ClinicalRecord`

---

## VULNERABILIDAD #5: Portal Routes Sin Middleware de Tenancy 🟠 ALTA

**Ubicación**: `routes/web.php:50-61`

Las rutas del patient portal solo tenían middleware `web` y `signed`, no incluían middleware de tenancy.

**Solución**: Agregado `InitializeTenancyByPath::class` al grupo de middleware del portal

---

## VULNERABILIDAD #6: Soft Deletes Sin Verificación de Clínica 🟡 MEDIA

**Ubicación**: `OdontogramsRelationManager.php:160-171`

Al eliminar odontogramas, no se verificaba explícitamente que el registro pertenece a la clínica actual.

**Solución**: Verificación `$record->clinic_id !== tenant('id')` → abort 403 antes de `$record->delete()`

---

## Matriz de Riesgo

| # | Vulnerabilidad | Severidad | Tipo | Impacto | Estado |
|---|---|---|---|---|---|
| 1 | IDOR Patient Portal Dashboard | 🔴 CRÍTICA | IDOR | Acceso a historial de otras clínicas | ✅ Corregido |
| 2 | IDOR Budget Acceptance | 🔴 CRÍTICA | IDOR | Manipulación de presupuestos | ✅ Corregido |
| 3 | Authorization Bypass | 🔴 CRÍTICA | Auth Bypass | Acceso sin restricciones | ✅ Corregido |
| 4 | Missing Tenant Scope | 🟠 ALTA | Data Leak | Lectura de registros de otras clínicas | ✅ Corregido |
| 5 | Portal Sin Middleware Tenancy | 🟠 ALTA | Config | Contexto de tenant no verificado | ✅ Corregido |
| 6 | Soft Deletes Sin Verificación | 🟡 MEDIA | Data Loss | Posible eliminación indebida | ✅ Corregido |

---

## Hardening de Seguridad (Abril 2026)

### Credenciales
- `.env.testing` creado y añadido a `.gitignore`
- `phpunit.xml` limpio de credenciales hardcodeadas (usan `.env.testing` para user/password)

### Validaciones de Modelo
- `Appointment`: validación de fechas pasadas
- `Appointment`: validación de solapamiento de horarios
- `Patient`: RUT único por clínica
- `CalendarWidget`: validación en drag-and-drop

### Rate Limiting
- Portal routes: 30 req/min por IP (`throttle:portal`)

### Aislamiento Mejorado
- `BudgetItem` ahora tiene trait `BelongsToClinic`
- `ActivityLogger` prioriza `tenant('id')` sobre `session('tenant_id')`
- Rutas de portal centralizadas en `web.php`

### Testing de Seguridad
- `SecurityTenantIsolationTest`: 9 tests de aislamiento
- `AuthorizationRbacTest`: tests de RBAC
- `AuthorizationPolicyTest`: tests de policies
- `HttpApiTest`: 20 tests de endpoints protegidos

---

## Verificación Rápida

```bash
# Verificar que authorization no está saltada
grep -r "shouldSkipAuthorization.*return true" app/

# Verificar queries con clinic_id
grep -r "clinic_id" app/Livewire/Odontogram.php

# Ejecutar tests de seguridad
php artisan test --filter=SecurityTenantIsolationTest
php artisan test --filter=AuthorizationRbacTest

# Verificar rate limiting
php artisan route:list --path=portal
```
