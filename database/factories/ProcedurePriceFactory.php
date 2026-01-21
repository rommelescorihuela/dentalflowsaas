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
            'procedure_name' => $this->faker->sentence(3),
            'price' => $this->faker->randomFloat(2, 20, 1000),
            'duration' => $this->faker->numberBetween(15, 120) . ' minutes',
            'description' => $this->faker->paragraph(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
