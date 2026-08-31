<?php

namespace Database\Factories;

use App\Models\ClinicSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClinicSettingFactory extends Factory
{
    protected $model = ClinicSetting::class;

    public function definition(): array
    {
        return [
            'primary_color' => '#06b6d4',
            'secondary_color' => '#0891b2',
            'accent_color' => '#0e7490',
            'dark_mode' => false,
            'landing_title' => $this->faker->company(),
            'landing_description' => $this->faker->sentence(),
            'landing_phone' => $this->faker->phoneNumber(),
            'landing_email' => $this->faker->safeEmail(),
            'landing_address' => $this->faker->address(),
            'landing_enabled' => false,
            'email_notifications' => true,
            'appointment_reminders' => true,
            'reminder_hours_before' => 24,
        ];
    }
}
