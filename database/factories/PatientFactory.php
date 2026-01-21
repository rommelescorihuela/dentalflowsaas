<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'rut' => $this->faker->unique()->numerify('##.###.###-K'),
            'birth_date' => $this->faker->date(),
            'medical_history' => [
                'diabetes' => $this->faker->boolean(10),
                'hypertension' => $this->faker->boolean(15),
                'notes' => $this->faker->sentence(),
            ],
            'allergies' => $this->faker->randomElements(['Penicillin', 'Latex', 'Dust', 'Peanuts'], rand(0, 3)),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
