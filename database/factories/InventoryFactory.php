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
            'name' => $this->faker->word(),
            'category' => $this->faker->randomElement(['Consumables', 'Instruments', 'Equipment', 'Other']),
            'supplier' => $this->faker->company(),
            'price' => $this->faker->randomFloat(2, 1, 500),
            'quantity' => $this->faker->numberBetween(0, 100),
            'low_stock_threshold' => $this->faker->numberBetween(5, 20),
            'unit' => $this->faker->randomElement(['pieces', 'boxes', 'liters']),
            'items_per_unit' => $this->faker->numberBetween(1, 50),
            'expiration_type' => $this->faker->randomElement(['Expirable', 'Inexpirable']),
            'expiration_date' => $this->faker->optional(0.7)->date(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
