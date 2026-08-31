<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\ServiceFeedback;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFeedbackFactory extends Factory
{
    protected $model = ServiceFeedback::class;

    public function definition(): array
    {
        return [
            'appointment_id' => Appointment::factory(),
            'patient_id' => Patient::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->optional()->paragraph(),
            'category' => $this->faker->randomElement(['atencion', 'limpieza', 'procedimiento', 'instalaciones', 'tiempo_espera']),
        ];
    }
}
