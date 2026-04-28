<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Budget;
use App\Models\ClinicalRecord;
use App\Models\Odontogram;
use App\Models\Patient;
use App\Models\ProcedurePrice;
use App\Models\User;
use App\Services\BudgetGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetGeneratorTest extends TestCase
{
    use RefreshDatabase;

    protected User $doctor;
    protected Patient $patient;
    protected Odontogram $odontogram;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
        $this->switchTenant('clinic-a');

        $this->doctor = $this->doctorA;

        $this->patient = Patient::create([
            'clinic_id' => tenant('id'),
            'name' => 'Test Patient',
            'email' => 'patient@test.com',
            'rut' => '12345678-9',
        ]);

        $this->odontogram = Odontogram::create([
            'clinic_id' => tenant('id'),
            'patient_id' => $this->patient->id,
            'name' => 'Initial Checkup',
            'date' => now(),
            'status' => 'in_progress',
        ]);
    }

    public function test_generate_budget_from_completed_odontogram(): void
    {
        ClinicalRecord::create([
            'clinic_id' => tenant('id'),
            'patient_id' => $this->patient->id,
            'odontogram_id' => $this->odontogram->id,
            'tooth_number' => 18,
            'surface' => 'top',
            'diagnosis_code' => 'caries',
            'treatment_status' => 'planned',
        ]);

        ClinicalRecord::create([
            'clinic_id' => tenant('id'),
            'patient_id' => $this->patient->id,
            'odontogram_id' => $this->odontogram->id,
            'tooth_number' => 21,
            'surface' => 'center',
            'diagnosis_code' => 'missing',
            'treatment_status' => 'planned',
        ]);

        $this->odontogram->update(['status' => 'completed']);

        $budget = Budget::where('odontogram_id', $this->odontogram->id)->first();

        $this->assertNotNull($budget);
        $this->assertEquals('draft', $budget->status);
        $this->assertEquals($this->patient->id, $budget->patient_id);
        $this->assertEquals(2, $budget->items()->count());
        $this->assertGreaterThan(0, $budget->total);
    }

    public function test_generate_budget_does_not_duplicate(): void
    {
        $this->odontogram->update(['status' => 'completed']);

        $budget1 = Budget::where('odontogram_id', $this->odontogram->id)->first();

        $generator = app(BudgetGenerator::class);
        $budget2 = $generator->generate($this->odontogram);

        $this->assertEquals($budget1->id, $budget2->id);
        $this->assertEquals(1, Budget::where('odontogram_id', $this->odontogram->id)->count());
    }

    public function test_generate_budget_with_empty_records(): void
    {
        $this->odontogram->update(['status' => 'completed']);

        $budget = Budget::where('odontogram_id', $this->odontogram->id)->first();

        $this->assertNotNull($budget);
        $this->assertEquals(0, $budget->total);
        $this->assertEquals(0, $budget->items()->count());
    }

    public function test_generate_budget_uses_procedure_price_when_matched(): void
    {
        $procedure = ProcedurePrice::create([
            'clinic_id' => tenant('id'),
            'procedure_name' => 'Obturación Premium',
            'diagnosis_code' => 'caries',
            'price' => 75000,
            'duration' => 45,
        ]);

        ClinicalRecord::create([
            'clinic_id' => tenant('id'),
            'patient_id' => $this->patient->id,
            'odontogram_id' => $this->odontogram->id,
            'tooth_number' => 16,
            'surface' => 'left',
            'diagnosis_code' => 'caries',
            'treatment_status' => 'planned',
        ]);

        $this->odontogram->update(['status' => 'completed']);

        $budget = Budget::where('odontogram_id', $this->odontogram->id)->first();

        $this->assertNotNull($budget);
        $this->assertEquals(75000, $budget->total);
        $this->assertEquals('Obturación Premium (Diente 16 - left)', $budget->items()->first()->treatment_name);
    }

    public function test_generate_budget_skips_completed_treatments(): void
    {
        ClinicalRecord::create([
            'clinic_id' => tenant('id'),
            'patient_id' => $this->patient->id,
            'odontogram_id' => $this->odontogram->id,
            'tooth_number' => 11,
            'surface' => 'top',
            'diagnosis_code' => 'caries',
            'treatment_status' => 'completed',
        ]);

        ClinicalRecord::create([
            'clinic_id' => tenant('id'),
            'patient_id' => $this->patient->id,
            'odontogram_id' => $this->odontogram->id,
            'tooth_number' => 12,
            'surface' => 'bottom',
            'diagnosis_code' => 'caries',
            'treatment_status' => 'planned',
        ]);

        $this->odontogram->update(['status' => 'completed']);

        $budget = Budget::where('odontogram_id', $this->odontogram->id)->first();

        $this->assertNotNull($budget);
        $this->assertEquals(1, $budget->items()->count());
    }

    public function test_budget_has_expiration_date(): void
    {
        ClinicalRecord::create([
            'clinic_id' => tenant('id'),
            'patient_id' => $this->patient->id,
            'odontogram_id' => $this->odontogram->id,
            'tooth_number' => 28,
            'surface' => 'center',
            'diagnosis_code' => 'crown',
            'treatment_status' => 'planned',
        ]);

        $this->odontogram->update(['status' => 'completed']);

        $budget = Budget::where('odontogram_id', $this->odontogram->id)->first();

        $this->assertNotNull($budget->expires_at);
        $this->assertTrue($budget->expires_at->gt(now()));
    }

    public function test_budget_notes_indicate_auto_generation(): void
    {
        $this->odontogram->update(['status' => 'completed']);

        $budget = Budget::where('odontogram_id', $this->odontogram->id)->first();

        $this->assertStringContainsString('automáticamente', $budget->notes);
        $this->assertStringContainsString('odontograma', $budget->notes);
    }
}
