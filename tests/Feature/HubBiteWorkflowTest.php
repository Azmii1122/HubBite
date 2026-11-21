<?php

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('dashboard redirects users based on their role', function () {
    $admin = User::factory()->admin()->create();
    $merchant = User::factory()->merchant()->create();
    $buyer = User::factory()->create(['role' => User::ROLE_BUYER]);

    $this->actingAs($admin)->get('/dashboard')->assertRedirect(route('admin.users.index'));
    $this->actingAs($merchant)->get('/dashboard')->assertRedirect(route('seller.dashboard'));
    $this->actingAs($buyer)->get('/dashboard')->assertRedirect(route('customer.dashboard'));
});

test('buyer can browse warung, checkout items, and view order history', function () {
    $merchantUser = User::factory()->merchant()->create();
    $buyer = User::factory()->create(['role' => User::ROLE_BUYER, 'username' => 'Andi Pembeli']);

    $category = Category::create(['name' => 'Makanan Berat', 'slug' => 'makanan-berat']);
    $store = Store::create([
        'user_id' => $merchantUser->user_id,
        'store_name' => 'Dafka Kitchen',
        'slug' => 'dafka-kitchen',
        'address' => 'Kantin A',
        'contact' => '08123456789',
    ]);
    $menu = Menu::create([
        'store_id' => $store->id,
        'category_id' => $category->id,
        'name' => 'Nasi Goreng Spesial',
        'slug' => 'nasi-goreng-spesial',
        'price' => 18000,
        'stock' => 25,
        'availability_status' => true,
    ]);

    $this->actingAs($buyer)->get(route('customer.dashboard'))->assertOk()->assertSee('Dafka Kitchen');
    $this->actingAs($buyer)->get(route('customer.warung.show', $store->slug))->assertOk()->assertSee('Nasi Goreng Spesial');
    $this->actingAs($buyer)->get(route('customer.checkout.index'))->assertOk();

    $response = $this->actingAs($buyer)->postJson(route('customer.checkout.process'), [
        'customer_name' => $buyer->username,
        'delivery_address' => 'Asrama Putra Gedung 2',
        'delivery_type' => 'reguler',
        'delivery_fee' => 3000,
        'notes' => 'Tidak pedas',
        'payment_method' => 'qris',
        'items' => [
            [
                'menu_id' => $menu->id,
                'store_id' => $store->id,
                'name' => $menu->name,
                'price' => 18000,
                'quantity' => 2,
            ],
        ],
        'subtotal' => 36000,
        'total' => 39000,
    ]);

    $response->assertOk()->assertJson(['status' => 'success']);
    $orderCode = $response->json('order_code');

    $this->assertDatabaseHas('orders', [
        'order_code' => $orderCode,
        'store_id' => $store->id,
        'user_id' => $buyer->user_id,
        'order_status' => Order::STATUS_NEW,
        'total' => 39000,
    ]);

    $this->actingAs($buyer)->get(route('customer.checkout.success', $orderCode))->assertOk()->assertSee($orderCode);
    $this->actingAs($buyer)->get(route('customer.orders.index'))->assertOk()->assertSee($orderCode);
});

test('merchant can manage menus and transition order status through kitchen workflow', function () {
    $merchantUser = User::factory()->merchant()->create();
    $category = Category::create(['name' => 'Paket Hemat', 'slug' => 'paket-hemat']);
    $store = Store::create([
        'user_id' => $merchantUser->user_id,
        'store_name' => 'Warung Geprek',
        'slug' => 'warung-geprek',
        'address' => 'Sukapura',
        'contact' => '08123',
    ]);

    $this->actingAs($merchantUser)->get(route('seller.dashboard'))->assertOk();
    $this->actingAs($merchantUser)->get(route('seller.menus.create'))->assertOk();

    $this->actingAs($merchantUser)->post(route('seller.menus.store'), [
        'name' => 'Ayam Geprek Jumbo',
        'category_id' => $category->id,
        'price' => 22000,
        'description' => 'Pedas mantap',
        'stock' => 15,
        'availability_status' => 1,
    ])->assertRedirect(route('seller.menus.index'));

    $menu = Menu::where('store_id', $store->id)->firstOrFail();

    $this->actingAs($merchantUser)->patch(route('seller.menus.toggle', $menu))->assertRedirect();
    expect($menu->fresh()->availability_status)->toBeFalse();

    $order = Order::create([
        'order_code' => 'HB-SELLER-01',
        'order_number' => '#ORD-S01',
        'store_id' => $store->id,
        'customer_name' => 'Budi',
        'delivery_address' => 'Kampus',
        'order_status' => Order::STATUS_NEW,
        'subtotal' => 22000,
        'total' => 22000,
    ]);

    $this->actingAs($merchantUser)->patch(route('seller.orders.updateStatus', $order), ['status' => 'cooking'])->assertRedirect();
    expect($order->fresh()->order_status)->toBe(Order::STATUS_COOKING);

    $this->actingAs($merchantUser)->patch(route('seller.orders.updateStatus', $order), ['status' => 'ready'])->assertRedirect();
    expect($order->fresh()->order_status)->toBe(Order::STATUS_READY);

    $this->actingAs($merchantUser)->patch(route('seller.orders.updateStatus', $order), ['status' => 'completed'])->assertRedirect();
    expect($order->fresh()->order_status)->toBe(Order::STATUS_COMPLETED);

    $this->actingAs($merchantUser)->get(route('seller.analytics.index'))->assertOk();
    $this->actingAs($merchantUser)->get(route('seller.settings.index'))->assertOk();
    $this->actingAs($merchantUser)->get(route('seller.help'))->assertOk();
});
