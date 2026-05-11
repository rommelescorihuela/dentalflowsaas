<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use Tests\TestCase;
use App\Models\Payment;
use App\Models\Patient;
use App\Models\Budget;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
    }

    public function test_payment_belongs_to_clinic(): void
    {
        $payment = Payment::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'amount' => 50000,
            'method' => 'cash',
            'status' => 'pending',
        ]);

        $this->assertEquals('clinic-a', $payment->clinic_id);
    }

    public function test_payment_can_be_linked_to_budget(): void
    {
        $budget = Budget::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'total' => 100000,
            'status' => 'accepted',
        ]);

        $payment = Payment::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'budget_id' => $budget->id,
            'amount' => 50000,
            'method' => 'transfer',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->assertEquals($budget->id, $payment->budget->id);
        $this->assertEquals('paid', $payment->status);
    }

    public function test_payment_status_workflow(): void
    {
        $payment = Payment::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'amount' => 100000,
            'method' => 'insurance',
            'status' => 'pending',
        ]);

        $this->assertEquals('pending', $payment->status);

        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->assertEquals('paid', $payment->status);
        $this->assertNotNull($payment->paid_at);
    }
}
