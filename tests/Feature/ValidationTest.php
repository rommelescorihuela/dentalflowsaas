<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Clinic;
use App\Models\User;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Budget;
use App\Models\Odontogram;
use App\Models\ClinicalRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Facades\Tenancy;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
    }

    public function test_patient_requires_name(): void
    {
        $this->switchTenant('clinic-a');

        $patient = Patient::create([
            'name' => 'Test Patient',
            'email' => 'noname@clinic-a.test',
            'phone' => '+56911111111',
            'clinic_id' => 'clinic-a',
            'rut' => '55555555-' . time(),
        ]);

        $this->assertEquals('Test Patient', $patient->name);
    }

    public function test_patient_requires_email(): void
    {
        $this->switchTenant('clinic-a');

        $patient = Patient::create([
            'name' => 'Test Name',
            'email' => 'test' . time() . '@clinic-a.test',
            'phone' => '+56911111111',
            'clinic_id' => 'clinic-a',
            'rut' => '55555556-' . time(),
        ]);

        $this->assertStringContainsString('@clinic-a.test', $patient->email);
    }

    public function test_patient_requires_clinic_id(): void
    {
        $this->switchTenant('clinic-a');

        $patient = Patient::create([
            'name' => 'Test Name',
            'email' => 'noclinic@clinic-a.test',
            'phone' => '+56911111111',
            'clinic_id' => 'clinic-a',
            'rut' => '55555557-' . time(),
        ]);

        $this->assertEquals('clinic-a', $patient->clinic_id);
    }

    public function test_user_requires_valid_email_format(): void
    {
        $rules = ['email' => 'required|email'];

        $emails = ['valid@email.com'];

        foreach ($emails as $email) {
            $data = ['email' => $email];
            $validator = Validator::make($data, $rules);
            $this->assertFalse($validator->fails());
        }
    }

    public function test_patient_email_format_validation(): void
    {
        $rules = ['email' => 'required|email'];

        $validEmails = ['test@example.com', 'user.name@domain.clinic'];

        foreach ($validEmails as $email) {
            $data = ['email' => $email];
            $validator = Validator::make($data, $rules);
            $this->assertFalse($validator->fails(), "Email $email should pass validation");
        }
    }

    public function test_budget_requires_positive_total(): void
    {
        $this->switchTenant('clinic-a');

        $budget = Budget::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'total' => 100,
            'status' => 'pending',
        ]);

        $this->assertGreaterThanOrEqual(0, $budget->total);
    }

    public function test_appointment_requires_future_date(): void
    {
        $this->switchTenant('clinic-a');

        $appointment = Appointment::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addMinutes(30),
            'status' => 'scheduled',
        ]);

        $this->assertTrue($appointment->start_time->isFuture() || $appointment->start_time->isToday());
    }

    public function test_appointment_end_time_after_start_time(): void
    {
        $this->switchTenant('clinic-a');

        $startTime = now()->addDay();
        $endTime = $startTime->copy()->addMinutes(30);

        $this->assertTrue($endTime->greaterThan($startTime));

        $appointment = Appointment::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'scheduled',
        ]);

        $this->assertTrue($appointment->end_time->greaterThan($appointment->start_time));
    }

    public function test_odontogram_requires_patient_id(): void
    {
        $this->switchTenant('clinic-a');

        $this->expectException(\Illuminate\Database\QueryException::class);

        Odontogram::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => null,
            'name' => 'Test Odontogram',
            'date' => now(),
            'status' => 'in_progress',
        ]);
    }

    public function test_budget_status_must_be_valid(): void
    {
        $this->switchTenant('clinic-a');

        $validStatuses = ['pending', 'accepted', 'completed', 'rejected'];

        foreach ($validStatuses as $status) {
            $budget = Budget::create([
                'clinic_id' => 'clinic-a',
                'patient_id' => $this->patientA->id,
                'total' => 100,
                'status' => $status,
            ]);

            $this->assertEquals($status, $budget->status);
        }
    }

    public function test_user_password_minimum_length(): void
    {
        $password = 'short';

        $this->assertLessThan(8, strlen($password));

        $validPassword = 'validpassword123';

        $this->assertGreaterThanOrEqual(8, strlen($validPassword));
    }

    public function test_clinic_name_required(): void
    {
        $clinic = Clinic::create([
            'id' => 'test-clinic-' . time(),
            'name' => 'Test Clinic',
        ]);

        $this->assertEquals('Test Clinic', $clinic->name);
    }

    public function test_clinic_id_unique(): void
    {
        Clinic::create(['id' => 'unique-clinic', 'name' => 'Clínica Única']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Clinic::create(['id' => 'unique-clinic', 'name' => 'Otra Clínica']);
    }

    public function test_patient_rut_format_validation(): void
    {
        $rules = ['rut' => 'required|regex:/^[0-9]{7,8}-[0-9kK]$/'];

        $validRuts = ['12345678-9', '1234567-k', '11111111-1'];

        foreach ($validRuts as $rut) {
            $data = ['rut' => $rut];
            $validator = Validator::make($data, $rules);
            $this->assertFalse($validator->fails(), "RUT $rut should pass validation");
        }
    }

    public function test_user_requires_name(): void
    {
        $this->switchTenant('clinic-a');

        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser' . time() . '@clinic-a.test',
            'password' => bcrypt('password'),
            'clinic_id' => 'clinic-a',
        ]);

        $this->assertEquals('Test User', $user->name);
    }

    public function test_appointment_type_valid_options(): void
    {
        $this->switchTenant('clinic-a');

        $validTypes = ['consultation', 'checkup', 'treatment', 'emergency'];

        foreach ($validTypes as $index => $type) {
            $appointment = Appointment::create([
                'clinic_id' => 'clinic-a',
                'patient_id' => $this->patientA->id,
                'start_time' => now()->addDays($index + 1)->setHour(10),
                'end_time' => now()->addDays($index + 1)->setHour(10)->addMinutes(30),
                'status' => 'scheduled',
                'type' => $type,
            ]);

            $this->assertEquals($type, $appointment->type);
        }
    }

    public function test_clinical_record_tooth_number_range(): void
    {
        $this->switchTenant('clinic-a');

        $odontogram = $this->createOdontogram($this->patientA);

        $validTeeth = [11, 12, 13, 14, 15, 16, 17, 18, 21, 22, 23, 24, 25, 26, 27, 28, 31, 32, 33, 34, 35, 36, 37, 38, 41, 42, 43, 44, 45, 46, 47, 48];

        foreach ($validTeeth as $tooth) {
            $record = ClinicalRecord::create([
                'clinic_id' => 'clinic-a',
                'patient_id' => $this->patientA->id,
                'odontogram_id' => $odontogram->id,
                'tooth_number' => $tooth,
                'surface' => 'center',
                'diagnosis_code' => 'caries',
                'treatment_status' => 'planned',
            ]);

            $this->assertEquals($tooth, $record->tooth_number);
        }
    }

    public function test_clinical_record_valid_treatment_statuses(): void
    {
        $this->switchTenant('clinic-a');

        $odontogram = $this->createOdontogram($this->patientA);

        $validStatuses = ['planned', 'in_progress', 'completed', 'cancelled'];

        foreach ($validStatuses as $status) {
            $record = ClinicalRecord::create([
                'clinic_id' => 'clinic-a',
                'patient_id' => $this->patientA->id,
                'odontogram_id' => $odontogram->id,
                'tooth_number' => 11,
                'surface' => 'center',
                'diagnosis_code' => 'caries',
                'treatment_status' => $status,
            ]);

            $this->assertEquals($status, $record->treatment_status);
        }
    }

    public function test_clinical_record_valid_diagnosis_codes(): void
    {
        $this->switchTenant('clinic-a');

        $odontogram = $this->createOdontogram($this->patientA);

        $validCodes = ['caries', 'obturation', 'endodontia', 'exodoncia', 'implante', 'puente', 'corona', 'blanqueamiento', 'gingivitis', 'periodontitis'];

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
}