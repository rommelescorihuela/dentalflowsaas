<?php

namespace Database\Factories;

use App\Models\DashboardBanner;
use Illuminate\Database\Eloquent\Factories\Factory;

class DashboardBannerFactory extends Factory
{
    protected $model = DashboardBanner::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'message' => $this->faker->optional()->paragraph(),
            'type' => $this->faker->randomElement(['info', 'success', 'warning', 'error', 'promo']),
            'color' => $this->faker->randomElement(['blue', 'green', 'yellow', 'red', 'purple', 'cyan']),
            'icon' => $this->faker->optional()->randomElement([
                'heroicon-o-information-circle',
                'heroicon-o-check-circle',
                'heroicon-o-exclamation-triangle',
                'heroicon-o-x-circle',
                'heroicon-o-sparkles',
            ]),
            'link' => $this->faker->optional()->url(),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(0, 100),
            'starts_at' => null,
            'ends_at' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}
