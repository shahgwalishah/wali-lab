<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
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
            'patient_no' => fake()->unique()->numerify('MLT-#####'),
            'name' => fake()->name(),
            'phone' => fake()->numerify('03##-#######'),
            'gender' => fake()->randomElement(['Male', 'Female']),
            'age' => fake()->numberBetween(1, 90),
            'city' => 'Multan',
            'referred_by' => 'Walk-in',
        ];
    }
}
