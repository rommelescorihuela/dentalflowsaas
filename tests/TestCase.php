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
use App\Models\BudgetItem;
use App\Models\Payment;
use App\Models\ProcedurePrice;
use App\Models\Inventory;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Stancl\Tenancy\Facades\Tenancy;
use Illuminate\Support\Facades\Auth;

abstract class TestCase extends BaseTestCase
{
    protected ?Clinic $clinicA = null;
    protected ?Clinic $clinicB = null;
    protected ?User $superAdmin = null;
    protected ?User $adminA = null;
    protected ?User $doctorA = null;
    protected ?User $assistantA = null;
    protected ?User $doctorB = null;
    protected ?User $assistantB = null;
    protected ?Patient $patientA = null;
    protected ?Patient $patientB = null;
    protected array $permissions = [];

    protected function setUpTenants(): void
    {
        $this->clinicA = Clinic::create(['id' => 'clinic-a', 'name' => 'Clínica Dental Sonrisas']);
        $this->clinicB = Clinic::create(['id' => 'clinic-b', 'name' => 'Ortodoncia Pérez']);

        $this->createRolesAndPermissions();

        Tenancy::initialize('clinic-a');
        
        $this->adminA = User::create([
            'name' => 'Admin Clínica A',
            'email' => 'admin@clinic-a.test',
            'password' => bcrypt('password'),
            'clinic_id' => 'clinic-a',
        ]);
        $this->adminA->assignRole('admin');

        $this->doctorA = User::create([
            'name' => 'Dr. Juan Pérez',
            'email' => 'doctor@clinic-a.test',
            'password' => bcrypt('password'),
            'clinic_id' => 'clinic-a',
        ]);
        $this->doctorA->assignRole('doctor');

        $this->assistantA = User::create([
            'name' => 'Asistente María',
            'email' => 'assistant@clinic-a.test',
            'password' => bcrypt('password'),
            'clinic_id' => 'clinic-a',
        ]);
        $this->assistantA->assignRole('assistant');

        $this->patientA = Patient::create([
            'name' => 'Paciente A',
            'email' => 'paciente-a@clinic-a.test',
            'phone' => '+56911111111',
            'clinic_id' => 'clinic-a',
            'rut' => '11111111-1',
        ]);

        Tenancy::initialize('clinic-b');
        
        $this->doctorB = User::create([
            'name' => 'Dr. Carlos López',
            'email' => 'doctor@clinic-b.test',
            'password' => bcrypt('password'),
            'clinic_id' => 'clinic-b',
        ]);
        $this->doctorB->assignRole('doctor');

        $this->assistantB = User::create([
            'name' => 'Asistente Ana',
            'email' => 'assistant@clinic-b.test',
            'password' => bcrypt('password'),
            'clinic_id' => 'clinic-b',
        ]);
        $this->assistantB->assignRole('assistant');

        $this->patientB = Patient::create([
            'name' => 'Paciente B',
            'email' => 'paciente-b@clinic-b.test',
            'phone' => '+56922222222',
            'clinic_id' => 'clinic-b',
            'rut' => '22222222-2',
        ]);

        Tenancy::initialize('clinic-a');
    }

    protected function createRolesAndPermissions(): void
    {
        $roles = ['super-admin', 'admin', 'doctor', 'assistant'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        $this->permissions = [
            'ViewAny:Clinic', 'View:Clinic', 'Create:Clinic', 'Update:Clinic', 'Delete:Clinic',
            'ViewAny:User', 'View:User', 'Create:User', 'Update:User', 'Delete:User',
            'ViewAny:Patient', 'View:Patient', 'Create:Patient', 'Update:Patient', 'Delete:Patient',
            'ViewAny:Appointment', 'View:Appointment', 'Create:Appointment', 'Update:Appointment', 'Delete:Appointment',
            'ViewAny:Budget', 'View:Budget', 'Create:Budget', 'Update:Budget', 'Delete:Budget',
            'ViewAny:Odontogram', 'View:Odontogram', 'Create:Odontogram', 'Update:Odontogram', 'Delete:Odontogram',
            'ViewAny:ClinicalRecord', 'View:ClinicalRecord', 'Create:ClinicalRecord', 'Update:ClinicalRecord', 'Delete:ClinicalRecord',
            'ViewAny:Payment', 'View:Payment', 'Create:Payment', 'Update:Payment', 'Delete:Payment',
            'ViewAny:ProcedurePrice', 'View:ProcedurePrice', 'Create:ProcedurePrice', 'Update:ProcedurePrice', 'Delete:ProcedurePrice',
            'ViewAny:Inventory', 'View:Inventory', 'Create:Inventory', 'Update:Inventory', 'Delete:Inventory',
            'ViewAny:Role', 'View:Role', 'Create:Role', 'Update:Role', 'Delete:Role',
        ];

        foreach ($this->permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $superAdmin = Role::findByName('super-admin');
        $superAdmin->syncPermissions($this->permissions);

        $admin = Role::findByName('admin');
        // Admin has same permissions as super-admin but is limited to single clinic
        // The distinction is enforced at the application level via clinic_id
        $admin->syncPermissions($this->permissions);

        $doctor = Role::findByName('doctor');
        $doctor->syncPermissions(array_filter($this->permissions, function($p) {
            return in_array(explode(':', $p)[0], ['ViewAny', 'View', 'Create', 'Update', 'Delete'], true)
                && in_array(explode(':', $p)[1], ['Patient', 'Appointment', 'Odontogram', 'ClinicalRecord', 'Budget', 'Payment']);
        }));

        $assistant = Role::findByName('assistant');
        $assistant->syncPermissions(array_filter($this->permissions, function($p) {
            return in_array(explode(':', $p)[0], ['ViewAny', 'View', 'Create', 'Update'], true)
                && in_array(explode(':', $p)[1], ['Patient', 'Appointment', 'Budget']);
        }));
    }

    protected function createSuperAdmin(): User
    {
        $this->superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@dentalflow.test'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'clinic_id' => null,
            ]
        );
        $this->superAdmin->assignRole('super-admin');
        return $this->superAdmin;
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

    protected function createAppointment(Patient $patient, ?User $doctor = null): Appointment
    {
        $randomMinutes = random_int(1, 500);
        return Appointment::create([
            'clinic_id' => $patient->clinic_id,
            'patient_id' => $patient->id,
            'user_id' => $doctor?->id,
            'start_time' => now()->addDays($randomMinutes)->setHour(10)->setMinute(0),
            'end_time' => now()->addDays($randomMinutes)->setHour(10)->setMinute(30),
            'status' => 'scheduled',
            'type' => 'consultation',
            'notes' => 'Cita de prueba',
        ]);
    }

    protected function createBudgetWithItems(Patient $patient, string $status = 'pending'): Budget
    {
        $budget = Budget::create([
            'clinic_id' => $patient->clinic_id,
            'patient_id' => $patient->id,
            'total' => 0,
            'status' => $status,
        ]);

        $items = [
            ['treatment_name' => 'Limpieza dental', 'cost' => 50000, 'quantity' => 1],
            ['treatment_name' => 'Empaste composite', 'cost' => 75000, 'quantity' => 2],
        ];

        foreach ($items as $item) {
            BudgetItem::create([
                'budget_id' => $budget->id,
                'treatment_name' => $item['treatment_name'],
                'cost' => $item['cost'],
                'quantity' => $item['quantity'],
            ]);
        }

        $budget->total = $budget->items->sum(fn($i) => $i->cost * $i->quantity);
        $budget->save();

        return $budget;
    }

    protected function createPayment(Budget $budget, Patient $patient): Payment
    {
        return Payment::create([
            'clinic_id' => $budget->clinic_id,
            'budget_id' => $budget->id,
            'patient_id' => $patient->id,
            'amount' => $budget->total / 2,
            'method' => 'cash',
            'paid_at' => now(),
        ]);
    }

    protected function createProcedurePrice(Clinic $clinic): ProcedurePrice
    {
        return ProcedurePrice::create([
            'clinic_id' => $clinic->id,
            'procedure_name' => 'Limpieza dental',
            'code' => 'D1110',
            'price' => 50000,
            'duration' => '30',
            'description' => 'Limpieza dental preventiva',
        ]);
    }

    protected function createInventoryItem(Clinic $clinic): Inventory
    {
        return Inventory::create([
            'clinic_id' => $clinic->id,
            'name' => 'Guantes desechables',
            'sku' => 'GUAN-001',
            'quantity' => 100,
            'min_quantity' => 20,
            'unit' => 'cajas',
            'price' => 5000,
        ]);
    }

    protected function actingAsSuperAdmin(): self
    {
        Auth::login($this->createSuperAdmin());
        return $this;
    }

    protected function actingAsAdmin(?User $admin = null): self
    {
        $this->switchTenant($admin?->clinic_id ?? 'clinic-a');
        Auth::login($admin ?? $this->adminA);
        return $this;
    }

    protected function actingAsAssistant(?User $assistant = null): self
    {
        $this->switchTenant($assistant?->clinic_id ?? 'clinic-a');
        Auth::login($assistant ?? $this->assistantA);
        return $this;
    }
}
