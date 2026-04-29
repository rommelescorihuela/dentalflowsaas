<?php

namespace App\Observers;

use App\Models\Odontogram;
use App\Services\BudgetGenerator;

class OdontogramObserver
{
    public function updated(Odontogram $odontogram): void
    {
        if ($odontogram->isDirty('status') && $odontogram->status === 'completed') {
            $budget = app(BudgetGenerator::class)->generate($odontogram);

            if ($budget && $budget->total > 0) {
                \Filament\Notifications\Notification::make()
                    ->title('Presupuesto generado')
                    ->body('Se creó automáticamente el presupuesto #' . $budget->id . ' por $' . number_format($budget->total, 0, ',', '.') . ' basado en los tratamientos pendientes.')
                    ->success()
                    ->send();
            } else {
                \Filament\Notifications\Notification::make()
                    ->title('Odontograma completado')
                    ->body('No hay tratamientos pendientes para generar un presupuesto.')
                    ->info()
                    ->send();
            }
        }
    }
}
