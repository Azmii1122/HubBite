<?php

use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('non-admin users cannot access admin order management', function () {
    $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

    $this->actingAs($buyer)
        ->get(route('admin.orders.index'))
        ->assertForbidden();
});

test('admin can list, create, update status, and delete orders', function () {
    $admin = User::factory()->admin()->create();
    $merchantUser = User::factory()->merchant()->create();
    $store = Store::create([
        'user_id' => $merchantUser->user_id,
        'store_name' => 'Dafka Kitchen',
        'slug' => 'dafka-kitchen',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.orders.store'), [
            'store_id' => $store->id,
            'customer_name' => 'Test Buyer',
            'item_name' => 'Nasi Goreng Spesial',
            'total_price' => 25000,
        ])
        ->assertRedirect(route('admin.orders.index'));

    $order = Order::where('customer_name', 'Test Buyer')->firstOrFail();

    $this->actingAs($admin)
        ->get(route('admin.orders.index', ['search' => $order->order_code]))
        ->assertOk()
        ->assertSee('Test Buyer')
        ->assertSee('Nasi Goreng Spesial');

    $this->actingAs($admin)
        ->put(route('admin.orders.update', $order), [
            'order_status' => Order::STATUS_COMPLETED,
            'payment_status' => 'paid',
        ])
        ->assertRedirect(route('admin.orders.index'));

    expect($order->fresh()->order_status)->toBe(Order::STATUS_COMPLETED)
        ->and($order->fresh()->payment_status)->toBe('paid');

    $this->actingAs($admin)
        ->delete(route('admin.orders.destroy', $order))
        ->assertRedirect(route('admin.orders.index'));

    $this->assertDatabaseMissing('orders', ['id' => $order->id]);
});
