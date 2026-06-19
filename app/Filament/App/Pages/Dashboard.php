<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Escritorio';

    protected static ?string $title = 'Escritorio';

    public function getTitle(): string
    {
        return 'Escritorio';
    }

    public function getHeading(): string
    {
        return 'Escritorio';
    }

    public function mount(): void
    {
        $tenant = tenant();

        if (! $tenant) {
            $user = auth()->user();

            if ($user && $user->clinic_id) {
                $tenantModel = config('tenancy.tenant_model');

                $found = $tenantModel::find($user->clinic_id);

                if ($found) {
                    tenancy()->initialize($found);

                    $tenant = $found;
                }
            }
        }

        if ($tenant) {
            $data = is_array($tenant->data)
                ? $tenant->data
                : json_decode($tenant->data ?? '{}', true);

            $step = $data['onboarding_step'] ?? 1;

            if ($step < 4) {
                redirect()->to('/app/onboarding-wizard');
            }
        }
    }
}
