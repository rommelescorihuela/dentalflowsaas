<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\Clinic;
use App\Models\User;
use App\Models\Patient;
use App\Models\Odontogram;
use App\Models\ClinicalRecord;
use App\Models\Budget;
use App\Models\Appointment;
use Stancl\Tenancy\Facades\Tenancy;
use Illuminate\Support\Facades\Auth;

abstract class TestCase extends BaseTestCase
{
    protected ?Clinic $clinicA = null;
    protected ?Clinic $clinicB = null;
    protected ?User $doctorA = null;
    protected ?User $doctorB = null;
    protected ?Patient $patientA = null;
    protected ?Patient $patientB = null;

    protected function setUpTenants(): void
    {
        $this->clinicA = Clinic::create(['id' => 'clinic-a', 'name' => 'Clínica A']);
        $this->clinicB = Clinic::create(['id' => 'clinic-b', 'name' => 'Clínica B']);

        Tenancy::initialize('clinic-a');
        $this->doctorA = User::create([
            'name' => 'Doctor A',
            'email' => 'doctor-a@clinic-a.test',
            'password' => bcrypt('password'),
            'clinic_id' => 'clinic-a',
        ]);
        $this->patientA = Patient::create([
            'name' => 'Paciente A',
            'email' => 'paciente-a@clinic-a.test',
            'phone' => '+56911111111',
            'clinic_id' => 'clinic-a',
            'rut' => '11111111-1',
        ]);

        Tenancy::initialize('clinic-b');
        $this->doctorB = User::create([
            'name' => 'Doctor B',
            'email' => 'doctor-b@clinic-b.test',
            'password' => bcrypt('password'),
            'clinic_id' => 'clinic-b',
        ]);
        $this->patientB = Patient::create([
            'name' => 'Paciente B',
            'email' => 'paciente-b@clinic-b.test',
            'phone' => '+56922222222',
            'clinic_id' => 'clinic-b',
            'rut' => '22222222-2',
        ]);

        Tenancy::initialize('clinic-a');
    }

    protected function switchTenant(string $tenantId): void
    {
        Tenancy::initialize($tenantId);
    }

    protected function actingAsDoctor(?User $doctor = null): self
    {
        if ($doctor) {
            $this->switchTenant($doctor->clinic_id);
            Auth::login($doctor);
        }
        return $this;
    }

    protected function createOdontogram(Patient $patient, string $status = 'in_progress'): Odontogram
    {
        return Odontogram::create([
            'clinic_id' => $patient->clinic_id,
            'patient_id' => $patient->id,
            'name' => 'Odontograma Test',
            'date' => now(),
            'status' => $status,
        ]);
    }

    protected function createClinicalRecord(
        Patient $patient,
        Odontogram $odontogram,
        int $toothNumber = 11,
        string $surface = 'center',
        string $diagnosis = 'caries'
    ): ClinicalRecord {
        return ClinicalRecord::create([
            'clinic_id' => $patient->clinic_id,
            'patient_id' => $patient->id,
            'odontogram_id' => $odontogram->id,
            'tooth_number' => $toothNumber,
            'surface' => $surface,
            'diagnosis_code' => $diagnosis,
            'treatment_status' => 'planned',
        ]);
    }

    protected function createBudget(Patient $patient, string $status = 'pending'): Budget
    {
        return Budget::create([
            'clinic_id' => $patient->clinic_id,
            'patient_id' => $patient->id,
            'total' => 100000,
            'status' => $status,
        ]);
    }

    protected function createAppointment(Patient $patient): Appointment
    {
        return Appointment::create([
            'clinic_id' => $patient->clinic_id,
            'patient_id' => $patient->id,
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addMinutes(30),
            'status' => 'scheduled',
        ]);
    }
}
