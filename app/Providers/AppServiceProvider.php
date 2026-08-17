<?php

namespace App\Providers;

use App\Helpers\ClinicHelper;
use App\Models\Activity;
use App\Models\Appointment;
use App\Models\Budget;
use App\Models\Clinic;
use App\Models\ClinicalRecord;
use App\Models\Inventory;
use App\Models\Odontogram;
use App\Models\Patient;
use App\Models\PatientMedicalHistory;
use App\Models\Permission;
use App\Models\Prescription;
use App\Models\ProcedurePrice;
use App\Models\Role;
use App\Models\SubscriptionPayment;
use App\Models\User;
use App\Observers\ClinicObserver;
use App\Observers\OdontogramObserver;
use App\Policies\ActivityPolicy;
use App\Policies\AppointmentPolicy;
use App\Policies\BudgetPolicy;
use App\Policies\ClinicalRecordPolicy;
use App\Policies\ClinicPolicy;
use App\Policies\InventoryPolicy;
use App\Policies\OdontogramPolicy;
use App\Policies\PatientMedicalHistoryPolicy;
use App\Policies\PatientPolicy;
use App\Policies\PrescriptionPolicy;
use App\Policies\ProcedurePricePolicy;
use App\Policies\RolePolicy;
use App\Policies\SubscriptionPaymentPolicy;
use App\Policies\UserPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\PermissionRegistrar;
use Stancl\Tenancy\Events\TenancyEnded;
use Stancl\Tenancy\Events\TenancyInitialized;

class AppServiceProvider extends ServiceProvider
{
    protected string $defaultTimezone;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Early injection of central domains from environment variable or defaults
        // This ensures the Tenancy package sees them even if config is cached incorrectly.
        $envDomains = env('TENANCY_CENTRAL_DOMAINS', '');
        $centralDomains = array_unique(array_merge(
            ['localhost', '127.0.0.1'],
            array_filter(array_map('trim', explode(',', $envDomains)))
        ));

        config(['tenancy.central_domains' => $centralDomains]);

        $this->defaultTimezone = config('app.timezone');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('portal', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        // Apply the tenant timezone whenever tenancy is initialized/ended.
        // This works both for subdomain/path initialization and for late initialization
        // in local dev (e.g. from the user's clinic_id after login).
        Event::listen(TenancyInitialized::class, fn () => $this->setTenantTimezone());
        Event::listen(TenancyEnded::class, fn () => $this->resetTimezone());

        Odontogram::observe(OdontogramObserver::class);
        Clinic::observe(ClinicObserver::class);

        app(PermissionRegistrar::class)
            ->setPermissionClass(Permission::class)
            ->setRoleClass(Role::class);

        // Superadmin bypass: grant all permissions unconditionally
        Gate::before(function ($user) {
            if ($user === null) {
                return null;
            }

            if (method_exists($user, 'hasRole') && $user->hasRole('super-admin')) {
                return true;
            }

            return null;
        });

        // Register Policies
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Clinic::class, ClinicPolicy::class);
        Gate::policy(Patient::class, PatientPolicy::class);
        Gate::policy(Appointment::class, AppointmentPolicy::class);
        Gate::policy(Budget::class, BudgetPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Inventory::class, InventoryPolicy::class);
        Gate::policy(ProcedurePrice::class, ProcedurePricePolicy::class);
        Gate::policy(Odontogram::class, OdontogramPolicy::class);
        Gate::policy(ClinicalRecord::class, ClinicalRecordPolicy::class);
        Gate::policy(SubscriptionPayment::class, SubscriptionPaymentPolicy::class);
        Gate::policy(Activity::class, ActivityPolicy::class);
        Gate::policy(PatientMedicalHistory::class, PatientMedicalHistoryPolicy::class);
        Gate::policy(Prescription::class, PrescriptionPolicy::class);

        // Set global URL default for tenant parameter if present in the path
        if (! app()->runningInConsole()) {
            $tenantId = request()->segment(1);

            // Special case for Livewire updates which are central but need tenant context
            if ($tenantId === 'livewire' && $referer = request()->header('referer')) {
                $path = parse_url($referer, PHP_URL_PATH);
                $pathSegments = explode('/', ltrim((string) $path, '/'));
                $firstSegment = $pathSegments[0] ?? null;

                if ($firstSegment && ! in_array($firstSegment, ['admin', 'up', 'login', 'register'])) {
                    $tenantId = $firstSegment;

                    if (! tenancy()->initialized) {
                        try {
                            tenancy()->initialize($tenantId);
                        } catch (\Exception $e) {
                            // If initialization fails, fall back to central
                        }
                    }
                }
            }

            // Set default if we found a valid tenant segment
            if ($tenantId && ! in_array($tenantId, ['admin', 'up', 'login', 'register', 'livewire'])) {
                URL::defaults(['tenant' => $tenantId]);
            }
        }
    }

    /**
     * Set the application timezone based on the current tenant's setting.
     * This ensures dates/times are displayed in the clinic's local timezone.
     */
    protected function setTenantTimezone(): void
    {
        if (app()->runningInConsole() || ! tenancy()->initialized) {
            return;
        }

        $timezone = ClinicHelper::getTimezone();

        if ($timezone && in_array($timezone, \DateTimeZone::listIdentifiers(), true)) {
            config(['app.timezone' => $timezone]);
            date_default_timezone_set($timezone);
        }
    }

    /**
     * Restore the default application timezone when tenancy ends.
     */
    protected function resetTimezone(): void
    {
        config(['app.timezone' => $this->defaultTimezone]);
        date_default_timezone_set($this->defaultTimezone);
    }
}
