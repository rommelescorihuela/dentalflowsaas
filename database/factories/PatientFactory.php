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
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'rut' => fake()->unique()->numerify('##.###.###-K'),
            'birth_date' => fake()->date(),
            'medical_history' => [
                'diabetes' => fake()->boolean(10),
                'hypertension' => fake()->boolean(15),
                'notes' => fake()->sentence(),
            ],
            'allergies' => fake()->randomElements(['Penicillin', 'Latex', 'Dust', 'Peanuts'], rand(0, 3)),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
