<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory>
 */
class InventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'category' => fake()->randomElement(['Consumables', 'Instruments', 'Equipment', 'Other']),
            'supplier' => fake()->company(),
            'price' => fake()->randomFloat(2, 1, 500),
            'quantity' => fake()->numberBetween(0, 100),
            'low_stock_threshold' => fake()->numberBetween(5, 20),
            'unit' => fake()->randomElement(['pieces', 'boxes', 'liters']),
            'items_per_unit' => fake()->numberBetween(1, 50),
            'expiration_type' => fake()->randomElement(['Expirable', 'Inexpirable']),
            'expiration_date' => fake()->optional(0.7)->date(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
