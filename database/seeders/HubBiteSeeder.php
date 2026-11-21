<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Store;
use App\Models\StoreVisit;
use App\Models\User;
use Illuminate\Database\Seeder;

class HubBiteSeeder extends Seeder
{
    /**
     * Seed HubBite catalog, stores, menus, and sample orders.
     */
    public function run(): void
    {
        $merchantUser = User::where('email', 'merchant@hubbite.com')->first();
        $geprekUser = User::where('email', 'geprek@hubbite.com')->first();
        $buyerUser = User::where('email', 'buyer@hubbite.com')->first();
        $sitiUser = User::where('email', 'siti@hubbite.com')->first();

        // 1. Categories
        $categoriesData = [
            ['name' => 'Makanan Berat', 'slug' => 'makanan-berat'],
            ['name' => 'Minuman Segar', 'slug' => 'minuman-segar'],
            ['name' => 'Camilan & Snack', 'slug' => 'camilan-snack'],
            ['name' => 'Paket Hemat', 'slug' => 'paket-hemat'],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['slug']] = Category::updateOrCreate(
                ['slug' => $cat['slug']],
                ['name' => $cat['name']]
            );
        }

        // 2. Stores (Unified Store / Merchant)
        $dafkaStore = Store::updateOrCreate(
            ['slug' => 'dafka-kitchen'],
            [
                'user_id' => $merchantUser?->user_id,
                'store_name' => 'Dafka Kitchen',
                'campus_hub' => 'Campus Hub - Central',
                'category' => 'Aneka Masakan Kampus',
                'description' => 'Sajian rumahan favorit mahasiswa dengan porsi mengenyangkan dan harga bersahabat.',
                'address' => 'Jl. Telekomunikasi No. 1, Kantin Gedung A',
                'contact' => '+62 812-3456-7890',
                'opening_time' => '08:00',
                'closing_time' => '21:00',
                'store_status' => 'open',
                'crowd_status' => 'medium',
                'is_live' => true,
                'is_verified' => true,
                'allow_delivery' => true,
                'is_popular' => true,
                'rating' => 4.9,
                'rating_count' => '1.5K+ Rating',
                'distance' => '0.4 km',
                'delivery_time' => '15-20 min',
                'wallet_balance' => 1250000,
            ]
        );

        $geprekStore = Store::updateOrCreate(
            ['slug' => 'warung-geprek-kampus'],
            [
                'user_id' => $geprekUser?->user_id,
                'store_name' => 'Warung Geprek Kampus',
                'campus_hub' => 'Campus Hub - Timur',
                'category' => 'Ayam & Sambal',
                'description' => 'Spesialis ayam geprek krispi level 1-10 dengan lalapan segar setiap hari.',
                'address' => 'Jl. Sukapura No. 22, Dayeuhkolot',
                'contact' => '+62 813-9876-5432',
                'opening_time' => '09:00',
                'closing_time' => '22:00',
                'store_status' => 'open',
                'crowd_status' => 'low',
                'is_live' => true,
                'is_verified' => true,
                'allow_delivery' => true,
                'is_popular' => true,
                'rating' => 4.8,
                'rating_count' => '980+ Rating',
                'distance' => '0.8 km',
                'delivery_time' => '20-30 min',
                'wallet_balance' => 850000,
            ]
        );

        // 3. Menus (Unified Menu / Product)
        $menus = [
            [
                'store_id' => $dafkaStore->id,
                'category_id' => $categories['makanan-berat']->id,
                'name' => 'Nasi Goreng Spesial Kampus',
                'slug' => 'nasi-goreng-spesial-kampus',
                'description' => 'Nasi goreng bumbu rempah dengan telur mata sapi, ayam suwir, dan kerupuk.',
                'price' => 18000,
                'stock' => 45,
                'availability_status' => true,
                'is_bestseller' => true,
                'is_popular_campus' => true,
            ],
            [
                'store_id' => $dafkaStore->id,
                'category_id' => $categories['paket-hemat']->id,
                'name' => 'Paket Ayam Bakar Madu + Es Teh',
                'slug' => 'paket-ayam-bakar-madu-es-teh',
                'description' => 'Ayam bakar olesan madu murni lengkap dengan nasi hangat, sambal terasi, dan es teh.',
                'price' => 25000,
                'stock' => 30,
                'availability_status' => true,
                'is_bestseller' => true,
                'is_popular_campus' => true,
            ],
            [
                'store_id' => $dafkaStore->id,
                'category_id' => $categories['minuman-segar']->id,
                'name' => 'Kopi Susu Gula Aren (Large)',
                'slug' => 'kopi-susu-gula-aren-large',
                'description' => 'Espresso arabika berpadu susu segar dan gula aren organik.',
                'price' => 15000,
                'stock' => 60,
                'availability_status' => true,
                'is_bestseller' => false,
                'is_popular_campus' => true,
            ],
            [
                'store_id' => $dafkaStore->id,
                'category_id' => $categories['camilan-snack']->id,
                'name' => 'Dimsum Ayam Udang (4 pcs)',
                'slug' => 'dimsum-ayam-udang',
                'description' => 'Dimsum kukus hangat dengan saus chili oil spesial.',
                'price' => 14000,
                'stock' => 25,
                'availability_status' => true,
                'is_bestseller' => false,
                'is_popular_campus' => false,
            ],
            [
                'store_id' => $geprekStore->id,
                'category_id' => $categories['paket-hemat']->id,
                'name' => 'Paket Ayam Geprek Sambal Bawang',
                'slug' => 'paket-ayam-geprek-sambal-bawang',
                'description' => 'Ayam geprek renyah dengan sambal bawang ulek dadakan dan nasi putih.',
                'price' => 20000,
                'stock' => 50,
                'availability_status' => true,
                'is_bestseller' => true,
                'is_popular_campus' => true,
            ],
            [
                'store_id' => $geprekStore->id,
                'category_id' => $categories['minuman-segar']->id,
                'name' => 'Es Jeruk Peras Segar',
                'slug' => 'es-jeruk-peras-segar',
                'description' => 'Perasan jeruk asli tanpa pemanis buatan.',
                'price' => 8000,
                'stock' => 40,
                'availability_status' => true,
                'is_bestseller' => false,
                'is_popular_campus' => false,
            ],
        ];

        $createdMenus = [];
        foreach ($menus as $menuData) {
            $createdMenus[$menuData['slug']] = Menu::updateOrCreate(
                ['slug' => $menuData['slug'], 'store_id' => $menuData['store_id']],
                $menuData
            );
        }

        // 4. Sample Orders (so Admin, Seller, and Buyer order views all have real data)
        if (Order::count() === 0) {
            $nasgor = $createdMenus['nasi-goreng-spesial-kampus'];
            $kopi = $createdMenus['kopi-susu-gula-aren-large'];
            $ayamBakar = $createdMenus['paket-ayam-bakar-madu-es-teh'];
            $geprek = $createdMenus['paket-ayam-geprek-sambal-bawang'];

            // Order 1: New order (for Seller to accept/cook/decline)
            $order1 = Order::create([
                'order_code' => 'HB-20261122-001',
                'order_number' => '#ORD-001',
                'user_id' => $buyerUser?->user_id,
                'store_id' => $dafkaStore->id,
                'customer_name' => $buyerUser?->username ?? 'Budi Sudarsono',
                'delivery_address' => 'Sukapura, Kec. Dayeuhkolot, Kabupaten Bandung',
                'customer_location' => 'Gedung Kuliah Umum Lt. 2',
                'service_type' => 'delivery',
                'delivery_type' => 'reguler',
                'delivery_fee' => 3000,
                'special_notes' => 'Sambal dipisah ya kak',
                'payment_method' => 'qris',
                'payment_status' => 'paid',
                'order_status' => Order::STATUS_NEW,
                'subtotal' => 33000,
                'total' => 36000,
                'received_at' => now()->subMinutes(5),
            ]);

            OrderItem::create([
                'order_id' => $order1->id,
                'menu_id' => $nasgor->id,
                'menu_name' => $nasgor->name,
                'price' => $nasgor->price,
                'quantity' => 1,
                'subtotal' => 18000,
            ]);
            OrderItem::create([
                'order_id' => $order1->id,
                'menu_id' => $kopi->id,
                'menu_name' => $kopi->name,
                'price' => $kopi->price,
                'quantity' => 1,
                'subtotal' => 15000,
            ]);
            Payment::create([
                'order_id' => $order1->id,
                'payment_method' => 'qris',
                'payment_status' => 'paid',
                'amount' => 36000,
                'paid_at' => now()->subMinutes(5),
            ]);

            // Order 2: Cooking order
            $order2 = Order::create([
                'order_code' => 'HB-20261122-002',
                'order_number' => '#ORD-002',
                'user_id' => $sitiUser?->user_id,
                'store_id' => $dafkaStore->id,
                'customer_name' => $sitiUser?->username ?? 'Siti Nurhaliza',
                'delivery_address' => 'Asrama Putri Gedung 3',
                'customer_location' => 'Meja 04 (Dine-in)',
                'service_type' => 'dine_in',
                'delivery_type' => 'pickup',
                'delivery_fee' => 0,
                'table_number' => '04',
                'payment_method' => 'tunai',
                'payment_status' => 'pending',
                'order_status' => Order::STATUS_COOKING,
                'subtotal' => 50000,
                'total' => 50000,
                'received_at' => now()->subMinutes(15),
                'cooking_at' => now()->subMinutes(8),
            ]);

            OrderItem::create([
                'order_id' => $order2->id,
                'menu_id' => $ayamBakar->id,
                'menu_name' => $ayamBakar->name,
                'price' => $ayamBakar->price,
                'quantity' => 2,
                'subtotal' => 50000,
            ]);
            Payment::create([
                'order_id' => $order2->id,
                'payment_method' => 'tunai',
                'payment_status' => 'pending',
                'amount' => 50000,
            ]);

            // Order 3: Completed order (for Analytics)
            $order3 = Order::create([
                'order_code' => 'HB-20261122-003',
                'order_number' => '#ORD-003',
                'user_id' => $buyerUser?->user_id,
                'store_id' => $dafkaStore->id,
                'customer_name' => $buyerUser?->username ?? 'Budi Sudarsono',
                'delivery_address' => 'Sukapura, Kec. Dayeuhkolot',
                'customer_location' => 'Takeaway Counter',
                'service_type' => 'takeaway',
                'delivery_type' => 'pickup',
                'delivery_fee' => 0,
                'payment_method' => 'qris',
                'payment_status' => 'paid',
                'order_status' => Order::STATUS_COMPLETED,
                'subtotal' => 18000,
                'total' => 18000,
                'preparation_time_minutes' => 11,
                'received_at' => now()->subHours(2),
                'cooking_at' => now()->subHours(2)->addMinutes(2),
                'ready_at' => now()->subHours(2)->addMinutes(13),
                'completed_at' => now()->subHours(2)->addMinutes(15),
            ]);

            OrderItem::create([
                'order_id' => $order3->id,
                'menu_id' => $nasgor->id,
                'menu_name' => $nasgor->name,
                'price' => $nasgor->price,
                'quantity' => 1,
                'subtotal' => 18000,
            ]);
            Payment::create([
                'order_id' => $order3->id,
                'payment_method' => 'qris',
                'payment_status' => 'paid',
                'amount' => 18000,
                'paid_at' => now()->subHours(2),
            ]);

            // Order 4: Second store sample order
            $order4 = Order::create([
                'order_code' => 'HB-20261122-004',
                'order_number' => '#ORD-004',
                'user_id' => $sitiUser?->user_id,
                'store_id' => $geprekStore->id,
                'customer_name' => $sitiUser?->username ?? 'Siti Nurhaliza',
                'delivery_address' => 'Jl. Telekomunikasi No. 12',
                'customer_location' => 'Fakultas Informatika',
                'service_type' => 'delivery',
                'delivery_type' => 'express',
                'delivery_fee' => 5000,
                'payment_method' => 'transfer',
                'payment_status' => 'paid',
                'order_status' => Order::STATUS_READY,
                'subtotal' => 20000,
                'total' => 25000,
                'received_at' => now()->subMinutes(25),
                'cooking_at' => now()->subMinutes(20),
                'ready_at' => now()->subMinutes(5),
            ]);

            OrderItem::create([
                'order_id' => $order4->id,
                'menu_id' => $geprek->id,
                'menu_name' => $geprek->name,
                'price' => $geprek->price,
                'quantity' => 1,
                'subtotal' => 20000,
            ]);
        }

        // 5. Sample StoreVisits for Analytics
        if (StoreVisit::count() === 0) {
            foreach (['192.168.1.10', '192.168.1.11', '192.168.1.12', '192.168.1.13'] as $idx => $ip) {
                StoreVisit::create([
                    'store_id' => $dafkaStore->id,
                    'ip_address' => $ip,
                    'visited_at' => now()->subDays($idx),
                    'converted' => $idx < 2,
                ]);
            }
        }
    }
}
