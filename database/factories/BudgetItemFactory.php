<?php

namespace Database\Factories;

use App\Models\Budget;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BudgetItem>
 */
class BudgetItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'budget_id' => Budget::factory(),
            'clinic_id' => fn(array $attributes) => Budget::find($attributes['budget_id'])?->clinic_id ?? 'clinic-a',
            'treatment_name' => fake()->randomElement([
                'Limpieza dental',
                'Empaste composite',
                'Extracción simple',
                'Endodoncia',
                'Corona porcelana',
                'Blanqueamiento',
            ]),
            'cost' => fake()->randomFloat(2, 10000, 150000),
            'quantity' => fake()->numberBetween(1, 5),
        ];
    }
}
