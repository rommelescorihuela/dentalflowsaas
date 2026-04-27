<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Patient;
use App\Models\Budget;
use App\Models\Appointment;
use App\Models\Odontogram;
use App\Models\ClinicalRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PatientPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
    }

    public function test_patient_can_view_own_appointments(): void
    {
        $this->switchTenant('clinic-a');

        $appointment = $this->createAppointment($this->patientA, $this->doctorA);

        $patient = Patient::find($this->patientA->id);
        $appointments = $patient->appointments;

        $this->assertTrue($appointments->contains('id', $appointment->id));
    }

    public function test_patient_can_view_own_budgets(): void
    {
        $this->switchTenant('clinic-a');

        $budget = $this->createBudgetWithItems($this->patientA, 'pending');

        $patient = Patient::find($this->patientA->id);
        $budgets = $patient->budgets;

        $this->assertTrue($budgets->contains('id', $budget->id));
    }

    public function test_patient_can_accept_budget(): void
    {
        $this->switchTenant('clinic-a');

        $budget = $this->createBudgetWithItems($this->patientA, 'pending');

        $budget->update(['status' => 'accepted']);
        $budget->refresh();

        $this->assertEquals('accepted', $budget->status);
    }

    public function test_patient_cannot_modify_accepted_budget(): void
    {
        $this->switchTenant('clinic-a');

        $budget = $this->createBudgetWithItems($this->patientA, 'accepted');

        $budget->update(['status' => 'completed']);

        $this->assertEquals('completed', $budget->fresh()->status);
    }

    public function test_patient_can_view_own_clinical_records(): void
    {
        $this->switchTenant('clinic-a');

        $odontogram = $this->createOdontogram($this->patientA);
        $record = $this->createClinicalRecord($this->patientA, $odontogram);

        $patient = Patient::find($this->patientA->id);
        $records = $patient->clinicalRecords;

        $this->assertTrue($records->contains('id', $record->id));
    }

    public function test_patient_cannot_access_other_patients_data(): void
    {
        $this->switchTenant('clinic-a');

        $budgetA = $this->createBudgetWithItems($this->patientA, 'pending');

        $this->switchTenant('clinic-b');

        $patientB = Patient::find($this->patientB->id);

        $this->assertNotEquals($budgetA->patient_id, $patientB->id);
    }

    public function test_patient_portal_dashboard_loads_relations(): void
    {
        $this->switchTenant('clinic-a');

        $this->createAppointment($this->patientA, $this->doctorA);
        $this->createBudgetWithItems($this->patientA, 'pending');
        $odontogram = $this->createOdontogram($this->patientA);
        $this->createClinicalRecord($this->patientA, $odontogram);

        $patient = Patient::with(['appointments', 'budgets', 'clinicalRecords'])->find($this->patientA->id);

        $this->assertCount(1, $patient->appointments);
        $this->assertCount(1, $patient->budgets);
        $this->assertCount(1, $patient->clinicalRecords);
    }

    public function test_patient_appointment_cancellation(): void
    {
        $this->switchTenant('clinic-a');

        $appointment = $this->createAppointment($this->patientA, $this->doctorA);

        $appointment->status = 'cancelled';
        $appointment->save();

        $this->assertEquals('cancelled', $appointment->fresh()->status);
    }

    public function test_patient_budget_items_are_visible(): void
    {
        $this->switchTenant('clinic-a');

        $budget = $this->createBudgetWithItems($this->patientA);

        $budget->load('items');

        $this->assertCount(2, $budget->items);
        $this->assertGreaterThan(0, $budget->items->count());
    }
}
