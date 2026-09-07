<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_no' => fake()->unique()->numerify('INV-######'),
            'patient_id' => Patient::factory(),
            'status' => 'completed',
            'total' => 1200,
            'discount' => 100,
            'paid' => 1100,
            'payment_method' => 'Cash',
            'reported_at' => now(),
        ];
    }
}
