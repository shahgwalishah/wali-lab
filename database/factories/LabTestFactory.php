<?php

namespace Database\Factories;

use App\Models\LabTest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LabTest>
 */
class LabTestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('TST-###'),
            'name' => fake()->words(3, true),
            'category' => 'Hematology',
            'price' => 1200,
            'sample_type' => 'Blood',
            'turnaround' => 'Same day',
            'active' => true,
        ];
    }
}
