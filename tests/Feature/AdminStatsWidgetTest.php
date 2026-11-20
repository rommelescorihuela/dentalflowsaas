<?php

namespace Tests\Feature;

use App\Enums\Plan;
use App\Filament\Widgets\AdminStatsOverview;
use App\Models\Clinic;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminStatsWidgetTest extends TestCase
{
    use RefreshDatabase;

    private function clinica(string $id): Clinic
    {
        return Clinic::create(['id' => $id, 'name' => 'Clínica '.$id]);
    }

    private function suscripcion(Clinic $clinic, Plan $plan, string $status): void
    {
        Subscription::create([
            'clinic_id' => $clinic->id,
            'plan' => $plan->value,
            'status' => $status,
            'trial_ends_at' => $status === 'trialing' ? now()->addDays(7) : null,
        ]);
    }

    public function test_el_mrr_suma_las_suscripciones_activas_segun_su_plan(): void
    {
        $this->suscripcion($this->clinica('a'), Plan::Pro, 'active');    // 89
        $this->suscripcion($this->clinica('b'), Plan::Basic, 'active');  // 39
        $this->suscripcion($this->clinica('c'), Plan::Pro, 'trialing');  // no cuenta

        Livewire::test(AdminStatsOverview::class)
            ->assertSuccessful()
            ->assertSee('$128.00')
            ->assertSee('2 clínicas de pago')
            ->assertSee('ARPU $64');
    }

    public function test_muestra_las_clinicas_en_prueba_y_su_vencimiento(): void
    {
        $this->suscripcion($this->clinica('a'), Plan::FreeTrial, 'trialing');

        Livewire::test(AdminStatsOverview::class)
            ->assertSuccessful()
            ->assertSee('En prueba')
            ->assertSee('La próxima vence el');
    }

    public function test_sin_suscripciones_el_mrr_es_cero_sin_porcentajes_negativos(): void
    {
        $this->clinica('a');

        Livewire::test(AdminStatsOverview::class)
            ->assertSuccessful()
            ->assertSee('$0.00')
            ->assertDontSee('-100%');
    }
}
