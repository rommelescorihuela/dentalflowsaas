<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Budget;
use App\Models\BudgetItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class PatientAndAppointmentsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
    }

    public function test_can_create_patient(): void
    {
        $this->switchTenant('clinic-a');

        $patient = Patient::create([
            'name' => 'Juan Pérez',
            'email' => 'juan@test.clinic-a.test',
            'phone' => '+56912345678',
            'clinic_id' => 'clinic-a',
            'rut' => '12345678-9',
            'birth_date' => '1990-05-15',
            'allergies' => json_encode(['Penicilina']),
            'medical_history' => json_encode(['Hipertensión']),
        ]);

        $this->assertEquals('12345678-9', $patient->rut);
        $this->assertEquals('Juan Pérez', $patient->name);
    }

    public function test_can_create_appointment(): void
    {
        $this->switchTenant('clinic-a');

        $appointment = Appointment::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'start_time' => Carbon::now()->addDays(2)->setHour(10)->setMinute(0),
            'end_time' => Carbon::now()->addDays(2)->setHour(10)->setMinute(30),
            'status' => 'scheduled',
            'type' => 'control',
            'notes' => 'Limpieza dental',
        ]);

        $this->assertEquals('control', $appointment->type);
        $this->assertEquals('scheduled', $appointment->status);
    }

    public function test_can_create_budget(): void
    {
        $this->switchTenant('clinic-a');

        $budget = Budget::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'total' => 150000,
            'status' => 'pending',
        ]);

        $this->assertEquals(150000, $budget->total);
        $this->assertEquals('pending', $budget->status);
    }

    public function test_budget_can_have_items(): void
    {
        $this->switchTenant('clinic-a');

        $budget = Budget::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'total' => 0,
            'status' => 'pending',
        ]);

        $items = [
            ['treatment_name' => 'Limpieza dental', 'cost' => 50000, 'quantity' => 1],
            ['treatment_name' => 'Empaste', 'cost' => 75000, 'quantity' => 2],
        ];

        foreach ($items as $itemData) {
            BudgetItem::create([
                'budget_id' => $budget->id,
                'treatment_name' => $itemData['treatment_name'],
                'cost' => $itemData['cost'],
                'quantity' => $itemData['quantity'],
            ]);
        }

        $budget->refresh();
        $budget->total = $budget->items->sum(fn($item) => $item->cost * $item->quantity);
        $budget->save();

        $this->assertEquals(200000, $budget->total);
    }

    public function test_patient_can_have_multiple_appointments(): void
    {
        $this->switchTenant('clinic-a');

        for ($i = 1; $i <= 5; $i++) {
            Appointment::create([
                'clinic_id' => 'clinic-a',
                'patient_id' => $this->patientA->id,
                'start_time' => Carbon::now()->addDays($i)->setHour(10)->setMinute(0),
                'end_time' => Carbon::now()->addDays($i)->setHour(10)->setMinute(30),
                'status' => 'scheduled',
            ]);
        }

        $count = Appointment::where('patient_id', $this->patientA->id)->count();
        $this->assertEquals(5, $count);
    }

    public function test_patient_can_have_multiple_budgets(): void
    {
        $this->switchTenant('clinic-a');

        Budget::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'total' => 100000,
            'status' => 'pending',
        ]);

        Budget::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'total' => 200000,
            'status' => 'accepted',
        ]);

        $count = Budget::where('patient_id', $this->patientA->id)->count();
        $this->assertEquals(2, $count);
    }

    public function test_appointment_isolation_between_clinics(): void
    {
        $this->switchTenant('clinic-a');

        $appointmentA = Appointment::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'start_time' => Carbon::now()->addDay()->setHour(9),
            'end_time' => Carbon::now()->addDay()->setHour(9)->addMinutes(30),
            'status' => 'scheduled',
        ]);

        $this->switchTenant('clinic-b');

        $appointmentB = Appointment::create([
            'clinic_id' => 'clinic-b',
            'patient_id' => $this->patientB->id,
            'start_time' => Carbon::now()->addDay()->setHour(9),
            'end_time' => Carbon::now()->addDay()->setHour(9)->addMinutes(30),
            'status' => 'scheduled',
        ]);

        $this->switchTenant('clinic-a');

        $appointmentsInA = Appointment::all()->count();
        $this->assertEquals(1, $appointmentsInA);
    }

    public function test_budget_status_transitions(): void
    {
        $this->switchTenant('clinic-a');

        $budget = Budget::create([
            'clinic_id' => 'clinic-a',
            'patient_id' => $this->patientA->id,
            'total' => 100000,
            'status' => 'pending',
        ]);

        $budget->status = 'accepted';
        $budget->save();

        $budget->status = 'completed';
        $budget->save();

        $budget->refresh();
        $this->assertEquals('completed', $budget->status);
    }

    public function test_patient_rut_is_unique_per_clinic(): void
    {
        $this->switchTenant('clinic-a');

        $uniqueRut = '60000001-' . time();

        $patient = Patient::create([
            'name' => 'Paciente 1',
            'email' => 'p1-' . time() . '@clinic-a.test',
            'phone' => '+56911111111',
            'clinic_id' => 'clinic-a',
            'rut' => $uniqueRut,
        ]);

        $this->assertEquals('Paciente 1', $patient->name);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Patient::create([
            'name' => 'Paciente Duplicado',
            'email' => 'p2-' . time() . '@clinic-a.test',
            'phone' => '+56922222222',
            'clinic_id' => 'clinic-a',
            'rut' => $uniqueRut,
        ]);
    }
}
