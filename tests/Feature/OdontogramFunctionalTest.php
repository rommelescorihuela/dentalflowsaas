<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Clinic;
use App\Models\User;
use App\Models\Patient;
use App\Models\Odontogram;
use App\Models\ClinicalRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Facades\Tenancy;
use Illuminate\Support\Facades\Auth;

class OdontogramFunctionalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
    }

    public function test_can_create_odontogram_session(): void
    {
        $this->switchTenant('clinic-a');
        
        $odontogram = Odontogram::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'name' => 'Consulta Inicial',
            'date' => now(),
            'status' => 'in_progress',
        ]);

        $this->assertNotNull($odontogram->id);
        $this->assertEquals('in_progress', $odontogram->status);
    }

    public function test_can_add_clinical_record_to_odontogram(): void
    {
        $this->switchTenant('clinic-a');
        
        $odontogram = Odontogram::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'name' => 'Consulta Test',
            'date' => now(),
            'status' => 'in_progress',
        ]);

        $record = ClinicalRecord::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'odontogram_id' => $odontogram->id,
            'tooth_number' => 11,
            'surface' => 'center',
            'diagnosis_code' => 'caries',
            'treatment_status' => 'planned',
            'notes' => 'Caries detected',
        ]);

        $this->assertNotNull($record->id);
        $this->assertEquals('caries', $record->diagnosis_code);
    }

    public function test_odontogram_can_have_multiple_records(): void
    {
        $this->switchTenant('clinic-a');
        
        $odontogram = Odontogram::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'name' => 'Consulta Multiple',
            'date' => now(),
            'status' => 'in_progress',
        ]);

        $surfaces = ['top', 'bottom', 'left', 'right', 'center'];
        
        foreach ($surfaces as $surface) {
            ClinicalRecord::create([
                'clinic_id' => 'clinic-a',
                'patient_id' => $this->patientA->id,
                'odontogram_id' => $odontogram->id,
                'tooth_number' => 11,
                'surface' => $surface,
                'diagnosis_code' => 'filled',
                'treatment_status' => 'completed',
            ]);
        }

        $records = ClinicalRecord::where('odontogram_id', $odontogram->id)->get();
        $this->assertCount(5, $records);
    }

    public function test_can_record_all_32_teeth(): void
    {
        $this->switchTenant('clinic-a');
        
        $odontogram = Odontogram::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'name' => 'Full Mouth',
            'date' => now(),
            'status' => 'in_progress',
        ]);

        $allTeeth = array_merge(
            [18, 17, 16, 15, 14, 13, 12, 11],
            [21, 22, 23, 24, 25, 26, 27, 28],
            [41, 42, 43, 44, 45, 46, 47, 48],
            [31, 32, 33, 34, 35, 36, 37, 38]
        );

        foreach ($allTeeth as $toothNumber) {
            ClinicalRecord::create([
                'clinic_id' => 'clinic-a',
                'patient_id' => $this->patientA->id,
                'odontogram_id' => $odontogram->id,
                'tooth_number' => $toothNumber,
                'surface' => 'center',
                'diagnosis_code' => 'healthy',
                'treatment_status' => 'existing',
            ]);
        }

        $count = ClinicalRecord::where('odontogram_id', $odontogram->id)->count();
        $this->assertEquals(32, $count);
    }

    public function test_can_have_multiple_odontogram_sessions(): void
    {
        $this->switchTenant('clinic-a');
        
        Odontogram::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'name' => 'Sesión 1',
            'date' => now()->subMonth(),
            'status' => 'completed',
        ]);

        Odontogram::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'name' => 'Sesión 2',
            'date' => now(),
            'status' => 'in_progress',
        ]);

        $odontograms = Odontogram::where('patient_id', $this->patientA->id)->get();
        $this->assertCount(2, $odontograms);
    }

    public function test_can_filter_by_diagnosis_code(): void
    {
        $this->switchTenant('clinic-a');
        
        $odontogram = Odontogram::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'name' => 'Diagnóstico',
            'date' => now(),
            'status' => 'completed',
        ]);

        ClinicalRecord::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'odontogram_id' => $odontogram->id,
            'tooth_number' => 11,
            'surface' => 'center',
            'diagnosis_code' => 'caries',
            'treatment_status' => 'planned',
        ]);

        ClinicalRecord::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'odontogram_id' => $odontogram->id,
            'tooth_number' => 21,
            'surface' => 'center',
            'diagnosis_code' => 'filled',
            'treatment_status' => 'completed',
        ]);

        $cariesRecords = ClinicalRecord::where('diagnosis_code', 'caries')->get();
        $this->assertCount(1, $cariesRecords);
    }

    public function test_clinical_records_isolated_between_sessions(): void
    {
        $this->switchTenant('clinic-a');
        
        $odontogram1 = Odontogram::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'name' => 'Sesión 1',
            'date' => now()->subMonth(),
            'status' => 'completed',
        ]);

        $odontogram2 = Odontogram::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'name' => 'Sesión 2',
            'date' => now(),
            'status' => 'in_progress',
        ]);

        ClinicalRecord::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'odontogram_id' => $odontogram1->id,
            'tooth_number' => 11,
            'surface' => 'center',
            'diagnosis_code' => 'caries',
            'treatment_status' => 'planned',
        ]);

        ClinicalRecord::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'odontogram_id' => $odontogram2->id,
            'tooth_number' => 11,
            'surface' => 'center',
            'diagnosis_code' => 'filled',
            'treatment_status' => 'completed',
        ]);

        $records1 = ClinicalRecord::where('odontogram_id', $odontogram1->id)->get();
        $records2 = ClinicalRecord::where('odontogram_id', $odontogram2->id)->get();

        $this->assertCount(1, $records1);
        $this->assertCount(1, $records2);
        $this->assertEquals('caries', $records1->first()->diagnosis_code);
        $this->assertEquals('filled', $records2->first()->diagnosis_code);
    }

    public function test_valid_diagnosis_codes(): void
    {
        $this->switchTenant('clinic-a');
        
        $odontogram = Odontogram::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'name' => 'Valid Codes',
            'date' => now(),
            'status' => 'in_progress',
        ]);

        $validCodes = ['caries', 'filled', 'endodontic', 'missing', 'crown', 'healthy'];

        foreach ($validCodes as $code) {
            $record = ClinicalRecord::create([
                'clinic_id' => 'clinic-a',
                'patient_id' => $this->patientA->id,
                'odontogram_id' => $odontogram->id,
                'tooth_number' => 11,
                'surface' => 'center',
                'diagnosis_code' => $code,
                'treatment_status' => 'planned',
            ]);
            $this->assertEquals($code, $record->diagnosis_code);
        }
    }

    public function test_valid_surfaces(): void
    {
        $this->switchTenant('clinic-a');
        
        $odontogram = Odontogram::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'name' => 'Valid Surfaces',
            'date' => now(),
            'status' => 'in_progress',
        ]);

        $validSurfaces = ['top', 'bottom', 'left', 'right', 'center', 'root'];

        foreach ($validSurfaces as $surface) {
            $record = ClinicalRecord::create([
                'clinic_id' => 'clinic-a',
                'patient_id' => $this->patientA->id,
                'odontogram_id' => $odontogram->id,
                'tooth_number' => 11,
                'surface' => $surface,
                'diagnosis_code' => 'healthy',
                'treatment_status' => 'existing',
            ]);
            $this->assertEquals($surface, $record->surface);
        }
    }

    public function test_treatment_status_options(): void
    {
        $this->switchTenant('clinic-a');
        
        $odontogram = Odontogram::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'name' => 'Treatment Status',
            'date' => now(),
            'status' => 'in_progress',
        ]);

        $statuses = ['planned', 'completed', 'existing'];

        foreach ($statuses as $status) {
            $record = ClinicalRecord::create([
                'clinic_id' => 'clinic-a',
                'patient_id' => $this->patientA->id,
                'odontogram_id' => $odontogram->id,
                'tooth_number' => 11,
                'surface' => 'center',
                'diagnosis_code' => 'filled',
                'treatment_status' => $status,
            ]);
            $this->assertEquals($status, $record->treatment_status);
        }
    }
}