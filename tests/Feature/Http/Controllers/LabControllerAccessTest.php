<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LabControllerAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_open_dashboard_with_identity(): void
    {
        $user = User::factory()->create([
            'name' => 'Lab Administrator',
            'email' => 'admin@medilab.pk',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Lab')
            ->where('auth.user.name', 'Lab Administrator')
            ->where('auth.user.email', 'admin@medilab.pk')
        );
    }

    public function test_dashboard_includes_repeat_patient_visit_history(): void
    {
        $user = User::factory()->create();
        $patient = Patient::factory()->create(['name' => 'Repeat Patient']);
        $olderOrder = Order::factory()->for($patient)->create(['invoice_no' => 'INV-HISTORY-1', 'created_at' => '2026-01-01 10:00:00']);
        $latestOrder = Order::factory()->for($patient)->create(['invoice_no' => 'INV-HISTORY-2', 'created_at' => '2026-02-01 10:00:00']);
        OrderItem::factory()->for($olderOrder)->create();
        OrderItem::factory()->for($latestOrder)->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertInertia(fn (Assert $page) => $page
            ->where('patients.0.name', 'Repeat Patient')
            ->where('patients.0.orders_count', 2)
            ->has('patients.0.orders', 2)
            ->where('patients.0.orders.0.invoice_no', 'INV-HISTORY-2')
            ->has('patients.0.orders.0.items', 1)
            ->has('patients.0.orders.0.items.0.test')
        );
    }
}
