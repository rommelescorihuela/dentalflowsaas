<?php

namespace Database\Factories;

use App\Models\Odontogram;
use App\Models\Patient;
use App\Models\ToothImage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ToothImageFactory extends Factory
{
    protected $model = ToothImage::class;

    public function definition(): array
    {
        return [
            'odontogram_id' => Odontogram::factory(),
            'patient_id' => Patient::factory(),
            'tooth_number' => $this->faker->numberBetween(11, 48),
            'image_type' => $this->faker->randomElement(['clinical', 'radiograph', 'before', 'after']),
            'file_path' => 'tooth-images/'.$this->faker->uuid().'.jpg',
            'file_name' => $this->faker->word().'.jpg',
            'description' => $this->faker->optional()->sentence(),
            'image_date' => $this->faker->dateTimeBetween('-1 year'),
            'uploaded_by' => User::factory(),
        ];
    }
}
