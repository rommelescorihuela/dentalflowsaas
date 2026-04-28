<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProcedurePrice>
 */
class ProcedurePriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'procedure_name' => fake()->sentence(3),
            'diagnosis_code' => fake()->randomElement(['caries', 'filled', 'endodontic', 'missing', 'crown', 'healthy']),
            'price' => fake()->randomFloat(2, 20, 1000),
            'duration' => fake()->numberBetween(15, 120),
            'description' => fake()->paragraph(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
