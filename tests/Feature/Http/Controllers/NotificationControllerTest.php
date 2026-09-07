<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\LabTest;
use App\Models\Order;
use App\Models\Patient;
use App\Models\User;
use App\Notifications\LabOrderStatusUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_lab_order_adds_unread_notification_for_user(): void
    {
        $user = User::factory()->create();
        $patient = Patient::factory()->create();
        $test = LabTest::factory()->create();

        $response = $this->actingAs($user)->post(route('orders.store'), [
            'patient_id' => $patient->id,
            'test_ids' => [$test->id],
            'discount' => 0,
            'paid' => 1200,
            'payment_method' => 'Cash',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('receipt_order_id', fn (int $orderId): bool => $orderId > 0);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $user->id,
            'read_at' => null,
        ]);
        $this->assertSame('new_order', $user->notifications()->first()->data['type']);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();
        $user->notify(new LabOrderStatusUpdated($order));

        $response = $this->actingAs($user)->patch(route('notifications.read-all'));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'All notifications marked as read.');
        $this->assertSame(0, $user->unreadNotifications()->count());
    }

    public function test_user_can_mark_own_notification_as_read(): void
    {
        $user = User::factory()->create();
        $user->notify(new LabOrderStatusUpdated(Order::factory()->create()));
        $notification = $user->unreadNotifications()->firstOrFail();

        $response = $this->actingAs($user)->patch(route('notifications.read', $notification));

        $response->assertRedirect();
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_cannot_mark_another_users_notification_as_read(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $owner->notify(new LabOrderStatusUpdated(Order::factory()->create()));
        $notification = $owner->unreadNotifications()->firstOrFail();

        $response = $this->actingAs($otherUser)->patch(route('notifications.read', $notification));

        $response->assertNotFound();
        $this->assertNull($notification->fresh()->read_at);
    }

    public function test_guest_cannot_mark_notifications_as_read(): void
    {
        $response = $this->patch('/notifications/read-all');

        $response->assertRedirect(route('login'));
    }
}
