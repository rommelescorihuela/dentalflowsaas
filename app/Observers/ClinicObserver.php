<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Clinic;
use Illuminate\Support\Facades\Log;

class ClinicObserver
{
    public function created(Clinic $clinic): void
    {
        $clinic->domains()->firstOrCreate([
            'domain' => "{$clinic->id}.localhost",
        ]);

        Log::info("Domain registered for clinic {$clinic->id}: {$clinic->id}.localhost");
    }

    public function deleted(Clinic $clinic): void
    {
        Log::info("Clinic {$clinic->id} deleted. Remove associated domains manually from hosting if needed.");
    }
}
