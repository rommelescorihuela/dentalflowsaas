<?php

namespace Database\Factories;

use App\Models\Odontogram;
use App\Models\Patient;
use App\Models\ToothNote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ToothNoteFactory extends Factory
{
    protected $model = ToothNote::class;

    public function definition(): array
    {
        return [
            'odontogram_id' => Odontogram::factory(),
            'patient_id' => Patient::factory(),
            'tooth_number' => $this->faker->numberBetween(11, 48),
            'note_type' => $this->faker->randomElement(['observation', 'diagnosis', 'treatment', 'follow_up']),
            'content' => $this->faker->paragraph(),
            'note_date' => $this->faker->dateTimeBetween('-1 year'),
            'created_by' => User::factory(),
        ];
    }
}
