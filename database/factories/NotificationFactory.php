<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['info', 'success', 'warning', 'error', 'appointment', 'payment']),
            'title' => $this->faker->sentence(),
            'message' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['unread', 'read']),
            'link' => $this->faker->optional()->url(),
            'icon' => $this->faker->optional()->word(),
            'color' => $this->faker->randomElement(['blue', 'green', 'yellow', 'red', 'purple']),
            'read_at' => $this->faker->optional()->dateTime(),
        ];
    }

    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'unread',
            'read_at' => null,
        ]);
    }
}
