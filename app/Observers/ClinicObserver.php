<?php

namespace App\Observers;

use App\Models\Clinic;
use App\Services\CpanelService;
use Illuminate\Support\Facades\Log;

class ClinicObserver
{
    public function __construct(
        protected CpanelService $cpanelService
    ) {
    }

    public function created(Clinic $clinic): void
    {
        $rootDomain = config('services.cpanel.root_domain');

        if (!$rootDomain) {
            Log::warning('CPANEL_ROOT_DOMAIN not configured, skipping domain registration for clinic ' . $clinic->id);
            return;
        }

        $fullDomain = "{$clinic->id}.{$rootDomain}";

        // Always register domain in database
        $clinic->domains()->firstOrCreate([
            'domain' => $fullDomain,
        ]);

        // Try to create subdomain in cPanel
        if ($this->cpanelService->isEnabled()) {
            $success = $this->cpanelService->createSubdomain($clinic->id, $rootDomain);

            if (!$success) {
                Log::warning("cPanel subdomain creation failed for {$fullDomain}. Domain was registered in DB but subdomain may need manual creation in cPanel.");
            }
        } else {
            Log::info("cPanel integration disabled. Domain {$fullDomain} registered in DB only. Create subdomain manually in cPanel.");
        }
    }

    public function deleted(Clinic $clinic): void
    {
        if ($this->cpanelService->isEnabled()) {
            $rootDomain = config('services.cpanel.root_domain');

            if ($rootDomain) {
                $this->cpanelService->deleteSubdomain($clinic->id, $rootDomain);
            }
        }
    }
}
