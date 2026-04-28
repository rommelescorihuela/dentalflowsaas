<?php

namespace App\Observers;

use App\Models\Odontogram;
use App\Services\BudgetGenerator;

class OdontogramObserver
{
    public function updated(Odontogram $odontogram): void
    {
        if ($odontogram->isDirty('status') && $odontogram->status === 'completed') {
            app(BudgetGenerator::class)->generate($odontogram);
        }
    }
}
