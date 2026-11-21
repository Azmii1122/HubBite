<?php

use App\Models\Category;
use App\Models\Menu;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unified store/merchant and menu/product models share the same tables and work with orders', function () {
    $merchantUser = User::factory()->merchant()->create();
    $buyerUser = User::factory()->create(['role' => User::ROLE_BUYER]);

    $category = Category::create([
        'name' => 'Makanan Berat',
        'slug' => 'makanan-berat',
    ]);

    $store = Store::create([
        'user_id' => $merchantUser->user_id,
        'store_name' => 'Kantin Teknik',
        'slug' => 'kantin-teknik',
        'address' => 'Gedung B',
        'contact' => '08123456789',
    ]);

    $menu = Menu::create([
        'store_id' => $store->id,
        'category_id' => $category->id,
        'name' => 'Nasi Ayam Katsu',
        'slug' => 'nasi-ayam-katsu',
        'price' => 22000,
        'stock' => 20,
        'availability_status' => true,
    ]);

    // Merchant & Product aliases read the exact same records
    expect(Merchant::find($store->id)?->name)->toBe('Kantin Teknik')
        ->and(Product::find($menu->id)?->name)->toBe('Nasi Ayam Katsu')
        ->and($merchantUser->fresh()->store?->id)->toBe($store->id);

    $order = Order::create([
        'order_code' => 'HB-TEST-001',
        'order_number' => '#ORD-TEST',
        'user_id' => $buyerUser->user_id,
        'store_id' => $store->id,
        'customer_name' => $buyerUser->username,
        'delivery_address' => 'Asrama Putra',
        'delivery_type' => 'reguler',
        'delivery_fee' => 3000,
        'payment_method' => 'qris',
        'payment_status' => 'paid',
        'order_status' => Order::STATUS_NEW,
        'subtotal' => 22000,
        'total' => 25000,
    ]);

    $item = OrderItem::create([
        'order_id' => $order->id,
        'menu_id' => $menu->id,
        'menu_name' => $menu->name,
        'price' => $menu->price,
        'quantity' => 1,
        'subtotal' => 22000,
    ]);

    expect($order->store->id)->toBe($store->id)
        ->and($order->merchant->id)->toBe($store->id)
        ->and($order->buyer->user_id)->toBe($buyerUser->user_id)
        ->and($order->items)->toHaveCount(1)
        ->and($item->product_name)->toBe('Nasi Ayam Katsu')
        ->and($order->canTransitionTo(Order::STATUS_COOKING))->toBeTrue()
        ->and($order->canTransitionTo(Order::STATUS_COMPLETED))->toBeFalse();
});
