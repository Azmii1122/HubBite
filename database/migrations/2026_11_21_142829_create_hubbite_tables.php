<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users', 'user_id')->nullOnDelete();
            $table->string('store_name');
            $table->string('slug')->unique();
            $table->string('campus_hub')->default('Campus Hub - Central');
            $table->string('category')->default('Aneka Makanan');
            $table->text('description')->nullable();
            $table->text('address')->nullable();
            $table->string('contact')->nullable();
            $table->string('opening_time', 10)->default('08:00');
            $table->string('closing_time', 10)->default('21:00');
            $table->string('store_status')->default('open'); // open, closed
            $table->string('crowd_status')->default('low'); // low, medium, high
            $table->boolean('is_live')->default(false);
            $table->boolean('is_verified')->default(true);
            $table->boolean('allow_delivery')->default(true);
            $table->boolean('is_popular')->default(false);
            $table->decimal('rating', 3, 1)->default(4.8);
            $table->string('rating_count')->default('1.2K+ Rating');
            $table->string('distance')->default('1.0 km');
            $table->string('delivery_time')->default('15-25 min');
            $table->string('avatar')->nullable();
            $table->string('banner_image')->nullable();
            $table->decimal('wallet_balance', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->string('image')->nullable();
            $table->integer('stock')->default(50);
            $table->boolean('availability_status')->default(true);
            $table->boolean('is_bestseller')->default(false);
            $table->boolean('is_popular_campus')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->string('order_number')->nullable()->index();
            $table->foreignId('user_id')->nullable()->constrained('users', 'user_id')->nullOnDelete();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->string('customer_name')->default('Pelanggan HubBite');
            $table->text('delivery_address')->nullable();
            $table->string('customer_location')->nullable();
            $table->string('service_type')->default('delivery'); // dine_in, takeaway, delivery
            $table->string('delivery_type')->default('reguler'); // reguler, express, pickup
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->string('table_number')->nullable();
            $table->text('special_notes')->nullable();
            $table->string('payment_method')->default('qris'); // qris, tunai, cash, transfer, hubpay
            $table->string('payment_status')->default('pending'); // pending, paid, failed
            $table->string('order_status')->default('new'); // new, cooking, ready, completed, declined, cancelled
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);
            $table->text('qris_code')->nullable();
            $table->boolean('is_priority')->default(false);
            $table->text('decline_reason')->nullable();
            $table->integer('preparation_time_minutes')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('cooking_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['store_id', 'order_status']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('menu_id')->nullable()->constrained('menus')->nullOnDelete();
            $table->string('menu_name');
            $table->decimal('price', 12, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 12, 2);
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('payment_method');
            $table->string('payment_status')->default('pending');
            $table->decimal('amount', 12, 2);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('store_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('visited_at')->nullable();
            $table->boolean('converted')->default(false);
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('store_visits');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('stores');
    }
};
