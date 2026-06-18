<?php

declare(strict_types=1);

namespace Tests\Feature\Subscription;

use App\Enums\Plan;
use App\Enums\SubscriptionStatus;
use App\Models\Clinic;
use App\Models\SubscriptionPayment;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
    }

    public function test_approve_payment_activates_subscription(): void
    {
        $clinic = Clinic::create(['id' => 'payment-test', 'name' => 'Payment Test']);
        $service = app(SubscriptionService::class);
        $service->createTrial($clinic);

        $payment = SubscriptionPayment::create([
            'clinic_id' => $clinic->id,
            'amount' => 99,
            'currency' => 'USD',
            'method' => 'zelle_usd',
            'status' => 'pending',
            'reference' => 'ZELLE-123',
            'paid_at' => now(),
        ]);

        $service->activate($clinic, Plan::Pro, $payment);

        $this->assertEquals(SubscriptionStatus::Active, $clinic->fresh()->subscription->status);
        $this->assertEquals('approved', $payment->fresh()->status);
        $this->assertNotNull($payment->fresh()->verified_at);
        $this->assertEquals(Plan::Pro, $clinic->fresh()->subscription->plan);
    }

    public function test_approve_payment_extends_active_subscription(): void
    {
        $clinic = Clinic::create(['id' => 'payment-test', 'name' => 'Payment Test']);
        $service = app(SubscriptionService::class);
        $service->createTrial($clinic);

        $payment1 = SubscriptionPayment::create([
            'clinic_id' => $clinic->id,
            'amount' => 99,
            'currency' => 'USD',
            'method' => 'zelle_usd',
            'status' => 'pending',
            'paid_at' => now(),
        ]);
        $service->activate($clinic, Plan::Pro, $payment1);

        $originalEnd = $clinic->fresh()->subscription->current_period_end;

        $payment2 = SubscriptionPayment::create([
            'clinic_id' => $clinic->id,
            'amount' => 99,
            'currency' => 'USD',
            'method' => 'binance_usdt',
            'status' => 'pending',
            'reference' => 'USDT-ABC123',
            'paid_at' => now(),
        ]);

        $service->extend($clinic, $payment2);

        $this->assertTrue($clinic->fresh()->subscription->current_period_end > $originalEnd);
        $this->assertEquals('approved', $payment2->fresh()->status);
    }

    public function test_reject_payment_does_not_activate(): void
    {
        $clinic = Clinic::create(['id' => 'payment-test', 'name' => 'Payment Test']);
        $service = app(SubscriptionService::class);
        $service->createTrial($clinic);

        $payment = SubscriptionPayment::create([
            'clinic_id' => $clinic->id,
            'amount' => 99,
            'currency' => 'USD',
            'method' => 'zelle_usd',
            'status' => 'pending',
            'paid_at' => now(),
        ]);

        $payment->update(['status' => 'rejected']);

        $this->assertEquals('rejected', $payment->fresh()->status);
        $this->assertEquals(SubscriptionStatus::Trialing, $clinic->fresh()->subscription->status);
    }

    public function test_payment_records_subscription_id_on_approval(): void
    {
        $clinic = Clinic::create(['id' => 'payment-test', 'name' => 'Payment Test']);
        $service = app(SubscriptionService::class);
        $service->createTrial($clinic);

        $payment = SubscriptionPayment::create([
            'clinic_id' => $clinic->id,
            'amount' => 49,
            'currency' => 'USD',
            'method' => 'pago_movil_bs',
            'status' => 'pending',
            'reference' => 'PM-001',
            'paid_at' => now(),
        ]);

        $service->activate($clinic, Plan::Basic, $payment);

        $this->assertNotNull($payment->fresh()->subscription_id);
        $this->assertEquals($clinic->fresh()->subscription->id, $payment->fresh()->subscription_id);
    }
}
