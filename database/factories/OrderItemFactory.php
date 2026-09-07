<?php

namespace Database\Factories;

use App\Models\LabTest;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'lab_test_id' => LabTest::factory(),
            'price' => 1200,
            'result' => '13.5',
            'unit' => 'g/dL',
            'reference_range' => '12.0 - 16.0',
            'flag' => 'normal',
        ];
    }
}
