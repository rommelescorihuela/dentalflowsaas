<?php

namespace App\Mail;

use App\Models\Budget;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BudgetSent extends Mailable
{
    use Queueable, SerializesModels;

    public Budget $budget;

    public function __construct(Budget $budget)
    {
        $this->budget = $budget;
    }

    public function build(): self
    {
        return $this->subject('Nuevo Presupuesto Disponible - DentalFlow')
            ->markdown('emails.budget.sent');
    }
}
