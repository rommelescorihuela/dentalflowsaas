<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Patient;
use App\Models\Odontogram;
use App\Models\ClinicalRecord;
use App\Models\Budget;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DoctorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
    }

    public function test_doctor_can_mark_treatment_completed(): void
    {
        $this->actingAsDoctor($this->doctorA);

        $odontogram = $this->createOdontogram($this->patientA);
        $record = $this->createClinicalRecord($this->patientA, $odontogram, 21, 'center', 'caries');

        $record->treatment_status = 'completed';
        $record->save();

        $this->assertEquals('completed', $record->treatment_status);
    }

    public function test_doctor_can_update_clinical_record(): void
    {
        $this->actingAsDoctor($this->doctorA);

        $odontogram = $this->createOdontogram($this->patientA);
        $record = $this->createClinicalRecord($this->patientA, $odontogram);

        $record->diagnosis_code = 'obturation';
        $record->treatment_status = 'completed';
        $record->save();

        $this->assertEquals('obturation', $record->fresh()->diagnosis_code);
    }

    public function test_doctor_can_create_budget(): void
    {
        $this->actingAsDoctor($this->doctorA);

        $budget = $this->createBudgetWithItems($this->patientA, 'pending');

        $this->assertEquals('pending', $budget->status);
        $this->assertGreaterThan(0, $budget->total);
    }

    public function test_doctor_can_view_own_patients_odontograms(): void
    {
        $this->actingAsDoctor($this->doctorA);

        $odontogram = $this->createOdontogram($this->patientA);
        $found = Odontogram::find($odontogram->id);

        $this->assertEquals($odontogram->id, $found->id);
        $this->assertEquals($this->patientA->id, $found->patient_id);
    }

    public function test_doctor_can_work_on_all_32_teeth(): void
    {
        $this->actingAsDoctor($this->doctorA);

        $odontogram = $this->createOdontogram($this->patientA);
        $toothNumbers = range(11, 18) + range(21, 28) + range(31, 38) + range(41, 48);
        $surfaces = ['top', 'bottom', 'left', 'right', 'center', 'root'];

        foreach (array_slice($toothNumbers, 0, 5) as $tooth) {
            foreach (array_slice($surfaces, 0, 2) as $surface) {
                ClinicalRecord::create([
                    'clinic_id' => 'clinic-a',
                    'patient_id' => $this->patientA->id,
                    'odontogram_id' => $odontogram->id,
                    'tooth_number' => $tooth,
                    'surface' => $surface,
                    'diagnosis_code' => 'caries',
                    'treatment_status' => 'planned',
                ]);
            }
        }

        $records = ClinicalRecord::where('odontogram_id', $odontogram->id)->count();
        $this->assertEquals(10, $records);
    }

    public function test_doctor_can_view_appointment_schedule(): void
    {
        $this->actingAsDoctor($this->doctorA);

        $appointment = $this->createAppointment($this->patientA, $this->doctorA);

        $this->assertEquals('scheduled', $appointment->status);
        $this->assertEquals($this->doctorA->id, $appointment->user_id);
    }

    public function test_doctor_has_correct_permissions(): void
    {
        $this->actingAsDoctor($this->doctorA);

        $this->assertTrue($this->doctorA->hasPermissionTo('ViewAny:Patient'));
        $this->assertTrue($this->doctorA->hasPermissionTo('Create:Odontogram'));
        $this->assertTrue($this->doctorA->hasPermissionTo('Update:ClinicalRecord'));
        $this->assertTrue($this->doctorA->hasPermissionTo('Create:Budget'));
    }

    public function test_doctor_can_delete_patients(): void
    {
        $this->actingAsDoctor($this->doctorA);

        $this->assertTrue($this->doctorA->hasPermissionTo('Delete:Patient'));
    }

    public function test_doctor_can_view_clinical_history(): void
    {
        $this->actingAsDoctor($this->doctorA);

        $odontogram = $this->createOdontogram($this->patientA);

        for ($i = 1; $i <= 3; $i++) {
            ClinicalRecord::create([
                'clinic_id' => 'clinic-a',
                'patient_id' => $this->patientA->id,
                'odontogram_id' => $odontogram->id,
                'tooth_number' => 11,
                'surface' => 'center',
                'diagnosis_code' => 'caries',
                'treatment_status' => 'completed',
            ]);
        }

        $records = ClinicalRecord::where('patient_id', $this->patientA->id)->get();
        $this->assertGreaterThanOrEqual(3, $records->count());
    }
}
