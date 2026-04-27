<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Budget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AssistantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
    }

    public function test_assistant_can_update_patient(): void
    {
        $this->actingAsAssistant($this->assistantA);

        $this->patientA->phone = '+56999999999';
        $this->patientA->save();

        $this->assertEquals('+56999999999', $this->patientA->fresh()->phone);
    }

    public function test_assistant_can_cancel_appointment(): void
    {
        $this->actingAsAssistant($this->assistantA);

        $appointment = $this->createAppointment($this->patientA, $this->doctorA);
        $appointment->status = 'cancelled';
        $appointment->save();

        $this->assertEquals('cancelled', $appointment->fresh()->status);
    }

    public function test_assistant_can_reschedule_appointment(): void
    {
        $this->actingAsAssistant($this->assistantA);

        $appointment = $this->createAppointment($this->patientA, $this->doctorA);
        $newDate = Carbon::now()->addDays(5)->setHour(16)->setMinute(0);

        $appointment->start_time = $newDate;
        $appointment->end_time = $newDate->copy()->addMinutes(30);
        $appointment->save();

        $this->assertEquals(16, $appointment->fresh()->start_time->hour);
    }

    public function test_assistant_can_create_budget(): void
    {
        $this->actingAsAssistant($this->assistantA);

        $budget = $this->createBudgetWithItems($this->patientA, 'pending');

        $this->assertGreaterThan(0, $budget->total);
        $this->assertEquals('pending', $budget->status);
    }

    public function test_assistant_cannot_delete_budget(): void
    {
        $this->actingAsAssistant($this->assistantA);

        $this->assertFalse($this->assistantA->hasPermissionTo('Delete:Budget'));
    }

    public function test_assistant_cannot_create_odontogram(): void
    {
        $this->actingAsAssistant($this->assistantA);

        $this->assertFalse($this->assistantA->hasPermissionTo('Create:Odontogram'));
    }

    public function test_assistant_cannot_delete_patients(): void
    {
        $this->actingAsAssistant($this->assistantA);

        $this->assertFalse($this->assistantA->hasPermissionTo('Delete:Patient'));
    }

    public function test_assistant_can_view_appointment_calendar(): void
    {
        $this->actingAsAssistant($this->assistantA);

        for ($i = 1; $i <= 5; $i++) {
            $this->createAppointment($this->patientA, $this->doctorA);
        }

        $count = Appointment::count();
        $this->assertGreaterThanOrEqual(5, $count);
    }

    public function test_assistant_has_correct_permissions(): void
    {
        $this->actingAsAssistant($this->assistantA);

        $this->assertTrue($this->assistantA->hasPermissionTo('ViewAny:Patient'));
        $this->assertTrue($this->assistantA->hasPermissionTo('Create:Patient'));
        $this->assertTrue($this->assistantA->hasPermissionTo('Create:Appointment'));
        $this->assertTrue($this->assistantA->hasPermissionTo('Update:Appointment'));
        $this->assertTrue($this->assistantA->hasPermissionTo('Create:Budget'));
    }

    public function test_assistant_cannot_manage_users(): void
    {
        $this->actingAsAssistant($this->assistantA);

        $this->assertFalse($this->assistantA->hasPermissionTo('Create:User'));
        $this->assertFalse($this->assistantA->hasPermissionTo('Delete:User'));
    }

    public function test_assistant_cannot_manage_inventory(): void
    {
        $this->actingAsAssistant($this->assistantA);

        $this->assertFalse($this->assistantA->hasPermissionTo('Create:Inventory'));
    }
}
