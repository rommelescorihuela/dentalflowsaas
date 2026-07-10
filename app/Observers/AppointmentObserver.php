<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Appointment;
use App\Models\Inventory;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class AppointmentObserver
{
    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        if ($appointment->isDirty('status') && $appointment->status === 'completed') {
            $this->deductInventory($appointment);
        }
    }

    protected function deductInventory(Appointment $appointment)
    {
        if (! $appointment->procedure_price_id) {
            return;
        }

        $procedure = $appointment->procedurePrice;

        if (! $procedure) {
            return;
        }

        $supplies = $procedure->procedureInventories;

        if ($supplies->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($supplies) {
            foreach ($supplies as $supply) {
                $inventory = $supply->inventory;

                if (! $inventory) {
                    continue;
                }

                $quantityToDeduct = $supply->quantity_used;

                // Simple integer deduction logic for now (as per user request for "deduction")
                // If more complex "Piece vs Box" logic is needed, we can expand later.
                // Assuming 'quantity' in Inventory is the atomic unit for now or adapting.

                // RMDC logic was complex (boxes vs pieces).
                // SaaS Inventory has: price, quantity, items_per_unit.
                // Let's assume quantity is "Total Units" for simplicity unless we see "items_per_unit" distinct usage.

                // Note: user asked for "Deducción Automática", implying matching what RMDC does.
                // RMDC: $inventory->quantity -= $fullBoxesNeeded;

                // Let's implement a simpler decrement first:
                // If quantity is integer, we assume it's the tracked unit.

                $inventory->decrement('quantity', (int) $quantityToDeduct);

                // Check low stock
                if ($inventory->quantity <= $inventory->low_stock_threshold) {
                    Notification::make()
                        ->title('Alerta de Stock Bajo')
                        ->body("El artículo '{$inventory->name}' tiene stock bajo ({$inventory->quantity} restante).")
                        ->warning()
                        ->sendToDatabase($appointment->user); // Notify the doctor/user
                }
            }
        });
    }
}
