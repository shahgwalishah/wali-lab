<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabControllerResultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_results_complete_order_and_create_notification(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['status' => 'processing', 'reported_at' => null]);
        $item = OrderItem::factory()->for($order)->create([
            'result' => null,
            'unit' => null,
            'reference_range' => null,
        ]);

        $response = $this->actingAs($user)->patch(route('orders.results', $order), [
            'items' => [[
                'id' => $item->id,
                'result' => '18.2',
                'unit' => 'g/dL',
                'reference_range' => '12.0 - 16.0',
                'flag' => 'high',
            ]],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Results saved and report completed.');
        $this->assertDatabaseHas('order_items', [
            'id' => $item->id,
            'result' => '18.2',
            'unit' => 'g/dL',
            'reference_range' => '12.0 - 16.0',
            'flag' => 'high',
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'completed',
        ]);
        $this->assertSame('report_ready', $user->notifications()->first()->data['type']);
    }

    public function test_result_entry_rejects_invalid_flag(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['status' => 'processing']);
        $item = OrderItem::factory()->for($order)->create(['result' => null]);

        $response = $this->actingAs($user)->from(route('dashboard'))->patch(route('orders.results', $order), [
            'items' => [[
                'id' => $item->id,
                'result' => '13.2',
                'flag' => 'critical',
            ]],
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHasErrors('items.0.flag');
        $this->assertNull($item->fresh()->result);
        $this->assertSame('processing', $order->fresh()->status);
    }
}
