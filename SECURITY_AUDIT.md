# DentalFlow SaaS - Informe de Vulnerabilidades de Seguridad

> Análisis realizado: 21 de Abril de 2026
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

### Código Vulnerable
```php
// routes/web.php:34
Route::get('/{tenant?}/portal/{patient}', [\App\Http\Controllers\PatientPortalController::class , 'dashboard'])->name('portal.dashboard');

// PatientPortalController.php:13-17
public function dashboard($patient)
{
    if (!($patient instanceof Patient)) {
        $patient = Patient::findOrFail($patient); // ❌ SIN VERIFICACIÓN DE TENANT
    }
    $patient->load(['appointments', 'budgets', 'clinicalRecords']);
    // ...
}
```

### Impacto
- Un atacante con cuenta en clínica A puede acceder al historial clínico completo de cualquier paciente de la clínica B
- Exposición de datos sensibles: alergias, historial médico, tratamientos realizados

### Pasos para explotar
1. Autenticarse en clínica A (ej: `clinic1.dentalflow.com`)
2. Crear una cita via portal para paciente de clínica A
3. Modificar el ID del paciente en la URL a un ID de otra clínica
4. Acceder a historial completo del paciente de otra clínica

### Solución Propuesta
```php
public function dashboard($patient)
{
    if (!($patient instanceof Patient)) {
        // Verificar que el paciente existe Y pertenece al tenant actual
        $patient = Patient::where('id', $patient)
            ->where('clinic_id', tenant('id'))
            ->firstOrFail();
    }

    // También verificar que el paciente autenticado tiene acceso
    $user = auth()->user();
    if (!$user || ($user->patient_id !== $patient->id && !$user->can('view', $patient))) {
        abort(403);
    }

    $patient->load(['appointments', 'budgets', 'clinicalRecords']);
    return view('patient-portal.dashboard', ['patient' => $patient]);
}
```

---

## VULNERABILIDAD #2: IDOR en Budget Acceptance 🔴 CRÍTICA

### Ubicación
- **Archivo**: `routes/web.php:36`
- **Controlador**: `app/Http/Controllers/PatientPortalController.php:25-32`

### Descripción
La ruta para aceptar presupuestos NO verifica que el presupuesto pertenece al paciente que está intentando aceptarlo.

### Código Vulnerable
```php
// routes/web.php:36
Route::post('/{tenant?}/portal/budgets/{budget}/accept', ...)
    ->name('portal.budgets.accept');

// PatientPortalController.php:25-32
public function acceptBudget(Budget $budget)
{
    $budget->update([
        'status' => 'accepted', // ❌ Sin verificación de propiedad
    ]);
    return back()->with('success', 'Budget accepted successfully!');
}
```

### Impacto
- Cualquier usuario puede aceptar/rechazar presupuestos de otras clínicas
- Manipulación de estado de presupuestos sin autorización

### Solución Propuesta
```php
public function acceptBudget(Budget $budget)
{
    // Verificar que el budget pertenece al tenant actual
    if ($budget->clinic_id !== tenant('id')) {
        abort(403);
    }

    // Verificar que el paciente autenticado es el dueño del budget
    $user = auth()->user();
    if ($user && $user->patient_id !== $budget->patient_id) {
        abort(403);
    }

    $budget->update(['status' => 'accepted']);
    return back()->with('success', 'Presupuesto aceptado.');
}
```

---

## VULNERABILIDAD #3: Authorization Bypass en RelationManager 🔴 CRÍTICA

### Ubicación
- **Archivo**: `app/Filament/App/Resources/Patients/RelationManagers/OdontogramsRelationManager.php:29-32`

### Descripción
El método `shouldSkipAuthorization()` retorna `true` permanentemente, deshabilitando TODA verificación de autorización para el RelationManager de Odontogramas.

### Código Vulnerable
```php
class OdontogramsRelationManager extends RelationManager
{
    protected static string $relationship = 'odontograms';

    public static function shouldSkipAuthorization(): bool
    {
        return true; // ❌ TODA autorización se salta
    }
    // ...
}
```

### Impacto
- Cualquier usuario (incluyendo asistentes con permisos limitados) puede:
  - Crear nuevos odontogramas
  - Editar odontogramas existentes
  - Eliminar odontogramas completos
  - Acceder al historial clínico
- Violación directa del modelo RBAC

### Solución Propuesta
```php
public static function shouldSkipAuthorization(): bool
{
    return false; // Usar autorización normal
}

// O implementar verificaciones específicas
public function canCreate(): bool
{
    return auth()->user()->can('create', Odontogram::class);
}

public function canEdit($record): bool
{
    return auth()->user()->can('update', $record);
}
```

---

## VULNERABILIDAD #4: Missing Tenant Scope en Odontogram Queries 🟠 ALTA

### Ubicación
- **Archivo**: `app/Livewire/Odontogram.php:107-110`

### Descripción
Al cargar datos existentes para populación del formulario, no se filtra por `clinic_id`.

### Código Vulnerable
```php
// Odontogram.php:107-110
$existing = ClinicalRecord::where('patient_id', $this->record->id)
    ->where('tooth_number', $this->selectedTooth)
    ->where('surface', $this->selectedSurfaces[0])
    ->first(); // ❌ Falta filtro clinic_id
```

### Impacto
- En ciertos edge cases, podría permitir leer registros de otras clínicas
- Depende de que el Global Scope funcione correctamente, pero no hay defensa en profundidad

### Solución Propuesta
```php
$existing = ClinicalRecord::where('clinic_id', tenant('id'))
    ->where('patient_id', $this->record->id)
    ->where('tooth_number', $this->selectedTooth)
    ->where('surface', $this->selectedSurfaces[0])
    ->first();
```

---

## VULNERABILIDAD #5: Portal Routes Sin Middleware de Tenancy 🟠 ALTA

### Ubicación
- **Archivo**: `routes/web.php:33-36`

### Descripción
Las rutas del patient portal solo tienen middleware `web` y `signed`, NO incluyen middleware de tenancy para verificar contexto.

### Código Vulnerable
```php
Route::middleware(['web', 'signed'])->group(function () { // ❌ Falta middleware tenancy
    Route::get('/{tenant?}/portal/{patient}', ...);
    Route::get('/{tenant?}/portal/{patient}/book', ...);
    Route::post('/{tenant?}/portal/budgets/{budget}/accept', ...);
});
```

### Impacto
- Las rutas son accesibles incluso si el contexto de tenancy no está inicializado
- Posible confusión de estado en aplicaciones multi-tenant

### Solución Propuesta
```php
Route::middleware([
    'web',
    'signed',
    'tenant' // Agregar middleware de tenancy
])->group(function () {
    // ...
});
```

---

## VULNERABILIDAD #6: Soft Deletes Sin Verificación de Clínica 🟡 MEDIA

### Ubicación
- **Archivo**: `app/Filament/App/Resources/Patients/RelationManagers/OdontogramsRelationManager.php:160-171`

### Descripción
Al eliminar odontogramas, no se verifica explícitamente que el registro pertenece a la clínica actual.

### Código Vulnerable
```php
\Filament\Actions\Action::make('delete')
    ->action(function (\App\Models\Odontogram $record) {
        $record->delete(); // ❌ Sin verificación de clinic_id
    }),
```

### Impacto
- Aunque Filament usa Model Binding, en teoría podría permitir eliminar registros de otras clínicas si hay bugs en el binding

### Solución Propuesta
```php
\Filament\Actions\Action::make('delete')
    ->action(function (\App\Models\Odontogram $record) {
        if ($record->clinic_id !== tenant('id')) {
            abort(403);
        }
        $record->delete();
    }),
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

## Correcciones Aplicadas

### V#1 - PatientPortalController.php
```php
// Ahora verifica que el paciente pertenece al tenant actual
$patient = Patient::where('id', $patient)
    ->where('clinic_id', tenant('id'))
    ->firstOrFail();
```

### V#2 - PatientPortalController.php
```php
// Verifica que el presupuesto pertenece al tenant actual
if ($budget->clinic_id !== tenant('id')) {
    abort(403);
}
```

### V#3 - OdontogramsRelationManager.php
```php
// Cambiado de return true a return false
public static function shouldSkipAuthorization(): bool
{
    return false;
}

// Agregados métodos de autorización
public function canCreate(): bool { ... }
public function canEdit($record): bool { ... }
public function canDelete($record): bool { ... }
public function canView($record): bool { ... }
```

### V#4 - Odontogram.php
```php
// Agregado filtro clinic_id en queries
$existing = ClinicalRecord::where('clinic_id', tenant('id'))
    ->where('patient_id', $this->record->id)
    ...
```

### V#5 - web.php
```php
// Agregado middleware de tenancy
Route::middleware([
    'web',
    'signed',
    \Stancl\Tenancy\Middleware\InitializeTenancyByPath::class,
])->group(function () { ... });
```

### V#6 - OdontogramsRelationManager.php
```php
// Verificación de clínica antes de eliminar
if ($record->clinic_id !== tenant('id')) {
    // Denegar acceso
    return;
}
$record->delete();
```

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
```