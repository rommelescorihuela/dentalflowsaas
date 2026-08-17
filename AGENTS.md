# AGENTS.md — DentalFlow SaaS

## Stack
- **Laravel 12, Filament 4.x, Livewire 3.x, Tailwind 4** vía `@tailwindcss/vite`.
- `resources/css/app.css` usa v4 `@import 'tailwindcss'` + `@source`/`@theme`. `tailwind.config.js` existe pero **no lo usa el build** (solo IDE).
- **Solo PostgreSQL ≥14** — `.env.example` trae `DB_CONNECTION=pgsql` por defecto.
- **Multi-tenancy**: Stancl Tenancy 3.9, **una sola base de datos compartida** aislada por `clinic_id` con scope global (`ClinicScope`). No hay cambio de conexión.
- **Auth/RBAC**: Spatie Permissions 6.0 con `teams => true`, `team_foreign_key => 'clinic_id'`. Clases `Permission`/`Role` personalizadas (en `config/permission.php`, registradas en `AppServiceProvider::boot()`). 4 roles: `super-admin`, `admin`, `doctor`, `assistant`. Nombrado de permisos: `<Action>:<Model>`.
- **Pagos**: solo manuales (Venezuela — transferencia, Pago móvil, Zelle, Binance). Sin pasarela; `subscriptions:process` maneja el ciclo de pago/suspensión manual.
- **Colas**: driver `database` (por defecto en `.env.example`). `composer run dev` ejecuta `queue:listen`.
- **E2E**: Playwright en `tests/e2e/` (3 spec files). Ejecutar con `npx playwright test` — `playwright.config.ts` levanta `php artisan serve` en :8000 automáticamente; requiere Postgres + datos sembrados.
- **Linting**: Laravel Pint por defecto — no hay `pint.json`. **Análisis estático**: `composer run analyse` (PHPStan, `app` @ level 5) **no funciona** — `larastan`/`phpstan` NO están en `composer.json`/`composer.lock`, así que `vendor/bin/phpstan` no existe; CI lo enmascara con `|| true` (no bloqueante).

## Comandos de Artisan

```bash
composer run setup       # install + copiar .env + key:generate + migrate + npm install + build (sin seed)
composer run dev         # serve + queue:listen --tries=1 + pail --timeout=0 + npm run dev (concurrently)
composer run test        # config:clear + php artisan test
composer run lint        # vendor/bin/pint
composer run analyse     # vendor/bin/phpstan analyse app --level=5

php artisan migrate:fresh --seed           # reset DB + seed 4 clínicas demo
php artisan diagnostic:all [--skip-tests]  # chequeo de salud del sistema
php artisan test:routes                    # diagnóstico de generación de rutas para URLs de tenant
php artisan subscriptions:process          # ciclo de vida diario de suscripciones
php artisan appointments:send-reminders    # recordatorios de citas
php artisan debug:tenant                   # dump de depuración del tenant
php artisan debug:check-schema {table}     # inspección de esquema
```

## Setup de Base de Datos y Tests
- **Requiere PostgreSQL**. `.env.example` trae `DB_CONNECTION=pgsql`; copiar a `.env`, completar `DB_USERNAME`/`DB_PASSWORD`.
- **DB de test**: `dentalflow_test` en `127.0.0.1:5432` (hardcodeada en `phpunit.xml`). Credenciales desde `.env.testing` (gitignored) o inyección por env. CI usa `postgres`/`postgres`.
- 34 archivos de test (33 Feature + 1 Unit stub). La clase base `Tests\TestCase` ofrece:
  - `setUpTenants()` — crea clínicas `clinic-a`/`clinic-b`, suscripciones Pro, 4 roles, permisos, pacientes. **Llamar en `setUp()`** para tests que necesiten tenant.
  - `switchTenant($id)`, `actingAsDoctor/Admin/Assistant($user)`, `actingAsSuperAdmin()`
  - Helpers de factory: `createOdontogram()`, `createClinicalRecord()`, `createBudget()`, `createBudgetWithItems()`, `createAppointment()`, `createPayment()`, `createProcedurePrice()`, `createInventoryItem()`
- Los tests usan `RefreshDatabase`. Los del portal requieren URLs `signed`.
- Super-admin omite toda autorización vía `Gate::before` en `AppServiceProvider::boot()`.

## Arquitectura de Tenancy

| Grupo de rutas | Middleware | Cómo se identifica el tenant |
|---|---|---|
| Panel app de Filament (`/app`) | `InitializeTenancyBySubdomainId` | Primer segmento del host al partir por `.` → ID de tenant. Omite `localhost`/`127`. |
| Portal del paciente | `InitializeTenancyByPath` | Ruta: `/{tenant}/portal/{patient}` |
| Admin central (`/admin`) | ninguno | Sin tenant — solo dominio central |

- **Dev local (`localhost`)**: Panel app en `http://127.0.0.1:8000/app`. Tenancy se resuelve desde el `clinic_id` del usuario logueado vía `ClinicScope`.
- `config('tenancy.central_domains')` = `['localhost', '127.0.0.1']` + env `TENANCY_CENTRAL_DOMAINS`. **Hay que configurarlo en producción** (ej. `dentalflow.digitalwebsolution.info`).
- **`TenancyServiceProvider` personalizado** registrado en `bootstrap/providers.php`. El provider del paquete Stancl está en `composer.json` `dont-discover` para que corra el nuestro. Overridea `BelongsToTenant::$tenantIdColumn = 'clinic_id'`, omite los jobs `CreateDatabase`/`MigrateDatabase`/`DeleteDatabase`.
- **Aislamiento de tenant**: 15 modelos usan el trait `BelongsToClinic` → agrega `ClinicScope`. No-op cuando `tenancy()->initialized` es false. Se sortea con `->withoutTenancy()` o `->withoutGlobalScope(ClinicScope::class)`.
- **Bootstrappers**: `DatabaseTenancyBootstrapper` **comentado** en `config/tenancy.php`. `CacheTenancyBootstrapper`, `FilesystemTenancyBootstrapper`, `QueueTenancyBootstrapper` habilitados: `storage_path()` se sufija con `tenant{clinic_id}` cuando tenancy está inicializado.
- **Timezone por tenant**: se setea en el evento `TenancyInitialized`, se restablece en `TenancyEnded` (`AppServiceProvider`).
- **Modelos personalizados**: `App\Models\Domain` (overridea el `Domain` del paquete, usa `clinic_id`), `App\Models\Clinic` (extiende `BaseTenant` con `VirtualColumn`).
- **Enums clave**: `app/Enums/Plan.php`, `app/Enums/SubscriptionStatus.php`.
- **IDs de clínica**: basados en strings (no UUIDs de Stancl). Los tests usan `clinic-a`, `clinic-b`.

## Stancl VirtualColumn — Crítico
- `Clinic` usa el trait `VirtualColumn`. Al cargar, el JSON `data` se decodifica en atributos individuales y luego se **setea a `null`**.
- Tras cargar: `$clinic->data` devuelve `null`. Leer/escribir atributos virtuales directo: `$clinic->currency = 'USD'`.
- **Nunca setear `$clinic->data = [...]`** — `encodeAttributes()` al guardar lo sobreescribiría.
- El accessor `$clinic->onboarding_step` cae a un query crudo de `DB::table('tenants')` cuando `data` está vacío.

## Paneles Filament

| Panel | ID | Ruta | Dir de recursos |
|---|---|---|---|
| Admin (central, default) | `admin` | `/admin` | `app/Filament/Resources/` |
| App (tenant) | `app` | `/app` | `app/Filament/App/Resources/` |

- Ambos: dark mode, sidebar colapsable. App=Cyan primario, Admin=Indigo.
- **`Section` en Filament 4** → `Filament\Schemas\Components\Section` (no `Filament\Forms\Components\Section`).
- Los schemas de recursos viven en subcarpetas `Schemas/` (ej. `Clinics/Schemas/ClinicForm.php`).

## Cadena de Middleware
- **Panel app**: `InitializeTenancyBySubdomainId → SetTenancyUrlDefaults → SyncSpatiePermissionsTeamId → Authenticate → EnsureSubscriptionActive`
- **Panel admin**: Filament estándar + `SyncSpatiePermissionsTeamId` (sin tenancy)
- Todos los usuarios entran por `/admin/login` (Filament central). El `clinic_id` del usuario + `SyncSpatiePermissionsTeamId` los marcan a su tenant después del login.
- `AppServiceProvider::boot()` setea `URL::defaults(['tenant' => ...])` desde el segmento 1 de la ruta e inicializa tenancy para updates de Livewire vía header `referer`.
- **`SetLocale`** + **`SyncSpatiePermissionsTeamId`** se agregan al grupo global `web` en `bootstrap/app.php`, no solo a los paneles. `SetLocale` lee la key `locale` de sesión (solo `en`/`es`, default `es`).

## Gotchas
- No existe `database/migrations/tenant/`; todas las migraciones de DB compartida están en `database/migrations/`, y los params `tenants:migrate`/`tenants:seed` de `config/tenancy.php` apuntan a ese mismo directorio. Es un único esquema compartido — los jobs por-tenant están deshabilitados, no confiar en ellos.
- Las rutas del portal usan URLs `signed` + `throttle:portal` (30 req/min por IP).
- `tailwind.config.js` existe pero **el build no lo usa** (Tailwind v4 vía `@tailwindcss/vite` lee las directivas `@source` del CSS).
- `config/permission.php` tiene `'teams' => true` y `'team_foreign_key' => 'clinic_id'`.
- **Auditoría (`spatie/laravel-activitylog` ^4.12)**: los 9 modelos (User, Clinic, Patient, Appointment, Budget, Payment, Odontogram, Treatment, SubscriptionPayment) usan el trait `App\Traits\LogsTenantActivity` → registra `created`/`updated`/`deleted` en la tabla `activity_log`. `clinic_id` se inyecta vía `tapActivity()`; IP/método/URL/UA vía el evento `creating` de `App\Models\Activity` (no guarda payload del request). El modelo `Activity` es inmutable (`updating`/`deleting` retornan false).
- **`activity_log` es especial**: `subject_id`/`causer_id` son columnas `string` (no bigint de `nullableMorphs`)** — los IDs de tenant (`clinic-a`) son strings. `DatabaseSeeder` usa `WithoutModelEvents`, así que el seed NO genera auditoría.
- **`InitializeTenancyBySubdomainId`** es middleware propio (no el de Stancl). Usa el subdominio **como ID de tenant directamente** (match por string contra `tenants.id`), no vía la tabla `domains`.
- **Observers**: `OdontogramObserver` (auto-presupuesto en `completed`) y `ClinicObserver` (al crear, registra una fila `{clinic.id}.localhost` en `domains` — habilita tenancy local por subdominio) se registran en `AppServiceProvider::boot()`. `AppointmentObserver` (descuenta inventario en `completed`) se registra inline en `Appointment::boot()` vía `self::observe()`.
- **`SubscriptionService::syncClinicDenormalized()`** escribe `plan` + `subscription_status` directo en la tabla `tenants` (datos duplicados más allá del modelo `Subscription`).
- **`ClinicSettingsController::save()`** escribe directo a `DB::table('tenants')` saltándose Eloquent.
- **`.env` real (dev local)**: `APP_URL=http://clinic1.localhost:8000` — los subdominios `{tenant}.localhost` resuelven a 127.0.0.1 y obtienen tenancy real por subdominio. En `127.0.0.1:8000/app` tenancy NO se inicializa vía middleware; las páginas `Dashboard`/`Billing` llaman `tenancy()->initialize($user->clinic_id)` de forma perezosa. `FILESYSTEM_DISK=public`. `APP_LOCALE=es`, `SESSION_DRIVER=file` y `TENANCY_CENTRAL_DOMAINS` ahora coinciden con `.env.example`.
- **`PermissionSeeder`** auto-descubre los recursos de Filament para generar permisos (7 por modelo: `ViewAny`..`ForceDelete`). Correr `setPermissionsTeamId($clinic->id)` antes de sembrar dentro del contexto del tenant.