<?php

namespace App\Observers;

use App\Models\Clinic;
use App\Services\CpanelService;

class ClinicObserver
{
    public function __construct(
        protected CpanelService $cpanelService
    ) {
    }

    public function created(Clinic $clinic): void
    {
        if ($this->cpanelService->isEnabled()) {
            $subdomain = $clinic->id;
            $rootDomain = config('services.cpanel.root_domain');

            if ($rootDomain) {
                $this->cpanelService->createSubdomain($subdomain, $rootDomain, $rootDomain);
            }
        }

        $rootDomain = config('services.cpanel.root_domain');
        if ($rootDomain) {
            $clinic->domains()->firstOrCreate([
                'domain' => "{$clinic->id}.{$rootDomain}",
            ]);
        }
    }

    public function deleted(Clinic $clinic): void
    {
        if ($this->cpanelService->isEnabled()) {
            $subdomain = $clinic->id;
            $rootDomain = config('services.cpanel.root_domain');

            if ($rootDomain) {
                $this->cpanelService->deleteSubdomain($subdomain, $rootDomain);
            }
        }
    }
}
