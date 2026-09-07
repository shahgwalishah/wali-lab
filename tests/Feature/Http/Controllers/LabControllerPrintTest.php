<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LabControllerPrintTest extends TestCase
{
    use RefreshDatabase;

    public function test_receipt_page_renders_order_and_printer_data(): void
    {
        $order = Order::factory()->create();
        OrderItem::factory()->for($order)->create();

        $response = $this->actingAs(User::factory()->create())->get(route('orders.receipt', $order));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Print/Receipt')
            ->where('order.id', $order->id)
            ->where('order.patient.id', $order->patient_id)
            ->has('order.items', 1)
            ->where('order.items.0.test.code', fn (string $code): bool => str_starts_with($code, 'TST-'))
        );
    }

    public function test_report_page_renders_results_and_reference_ranges(): void
    {
        $order = Order::factory()->create();
        $item = OrderItem::factory()->for($order)->create([
            'result' => '13.5',
            'reference_range' => '12.0 - 16.0',
        ]);

        $response = $this->actingAs(User::factory()->create())->get(route('orders.report', $order));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Print/Report')
            ->where('order.invoice_no', $order->invoice_no)
            ->where('order.items.0.result', '13.5')
            ->where('order.items.0.reference_range', '12.0 - 16.0')
            ->where('order.items.0.test.id', $item->lab_test_id)
        );
    }

    public function test_unknown_order_print_page_returns_not_found(): void
    {
        $response = $this->actingAs(User::factory()->create())->get('/orders/999999/receipt');

        $response->assertNotFound();
    }
}
