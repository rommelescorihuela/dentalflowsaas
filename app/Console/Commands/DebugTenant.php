<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Clinic;

class DebugTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:tenant';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug Clinic Data Persistence';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("--- Debugging Clinic Data Persistence ---");

        // 1. Create or Get Clinic
        $clinic = Clinic::first();
        if (!$clinic) {
            $this->info("Creating new clinic...");
            $clinic = Clinic::create([
                'id' => 'debug-clinic-' . time(),
                'name' => 'Debug Clinic',
            ]);
        }
        $this->info("Using Clinic ID: " . $clinic->id);

        // 2. Set Data via Explicit Array Update
        $this->info("Setting schedule_start via 'data' array...");
        $currentData = $clinic->data ?? [];
        $currentData['schedule_start'] = '10:00';
        $clinic->update(['data' => $currentData]);

        // 3. Refresh from DB
        $this->info("Refreshing from DB...");
        $clinic->refresh();
        $this->info("Clinic->data after refresh: " . json_encode($clinic->data));

        // 4. Fetch Fresh instance
        $this->info("Fetching fresh instance...");
        $fresh = Clinic::find($clinic->id);
        $this->info("Fresh->data: " . json_encode($fresh->data));

        // 5. Initialize Tenancy
        $this->info("Initializing Tenancy...");
        tenancy()->initialize($clinic);
        $this->info("tenant()->data: " . json_encode(tenant()->data));

        if (isset($clinic->data['schedule_start']) && $clinic->data['schedule_start'] === '10:00') {
            $this->info("✅ Success: Data persistence works.");
        } else {
            $this->error("❌ Fail: Data persistence broken.");
        }
    }
}
