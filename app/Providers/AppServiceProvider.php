<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Permission\PermissionRegistrar;
use App\Models\Permission;
use App\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app(PermissionRegistrar::class)
            ->setPermissionClass(Permission::class)
            ->setRoleClass(Role::class);

        // Register Policies
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Role::class, \App\Policies\RolePolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Clinic::class, \App\Policies\ClinicPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Patient::class, \App\Policies\PatientPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Appointment::class, \App\Policies\AppointmentPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Budget::class, \App\Policies\BudgetPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\User::class, \App\Policies\UserPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Inventory::class, \App\Policies\InventoryPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\ProcedurePrice::class, \App\Policies\ProcedurePricePolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Odontogram::class, \App\Policies\OdontogramPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\ClinicalRecord::class, \App\Policies\ClinicalRecordPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\SubscriptionPayment::class, \App\Policies\SubscriptionPaymentPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\SystemActivity::class, \App\Policies\SystemActivityPolicy::class);

        // Set global URL default for tenant parameter if present in the path
        if (!app()->runningInConsole()) {
            $tenantId = request()->segment(1);

            // Special case for Livewire updates which are central but need tenant context
            if ($tenantId === 'livewire' && $referer = request()->header('referer')) {
                $path = parse_url($referer, PHP_URL_PATH);
                $pathSegments = explode('/', ltrim($path, '/'));
                $firstSegment = $pathSegments[0] ?? null;

                if ($firstSegment && !in_array($firstSegment, ['admin', 'up', 'login', 'register'])) {
                    $tenantId = $firstSegment;
                    
                    if (!tenancy()->initialized) {
                        try {
                            tenancy()->initialize($tenantId);
                        } catch (\Exception $e) {
                            // If initialization fails, fall back to central
                        }
                    }
                }
            }

            // Set default if we found a valid tenant segment
            if ($tenantId && !in_array($tenantId, ['admin', 'up', 'login', 'register', 'livewire'])) {
                if (!isset(\Illuminate\Support\Facades\URL::getDefaultParameters()['tenant'])) {
                    \Illuminate\Support\Facades\URL::defaults(['tenant' => $tenantId]);
                }
            }
        }
    }
}
