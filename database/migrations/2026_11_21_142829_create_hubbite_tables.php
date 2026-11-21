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
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('category')->default('Aneka Makanan');
            $table->decimal('rating', 3, 1)->default(4.8);
            $table->string('rating_count')->default('1.2K+ Rating');
            $table->string('status')->default('Buka'); // Buka, Tutup
            $table->string('distance')->default('1.2 km');
            $table->string('delivery_time')->default('20-30 min');
            $table->boolean('is_popular')->default(false);
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('category')->default('ANEKA MAKANAN');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_bestseller')->default(false);
            $table->boolean('is_popular_campus')->default(false);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('customer_name')->default('Budi Sudarsono');
            $table->text('delivery_address');
            $table->string('delivery_type')->default('reguler'); // reguler, express
            $table->decimal('delivery_fee', 12, 2)->default(3000);
            $table->text('notes')->nullable();
            $table->string('payment_method')->default('qris'); // tunai, qris, transfer
            $table->string('payment_status')->default('pending'); // pending, paid
            $table->string('order_status')->default('diproses'); // diproses, dikirim, selesai
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);
            $table->text('qris_code')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('product_name');
            $table->decimal('price', 12, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 12, 2);
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
        Schema::dropIfExists('merchants');
    }
};

