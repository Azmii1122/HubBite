<?php

namespace Database\Seeders;

use App\Models\Pesanan;
use Illuminate\Database\Seeder;

class PesananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sampleOrders = [
            [
                'customer_name' => 'Dimas Pratama',
                'item_name' => 'Paket Ayam Geprek Sambal Bawang + Es Teh',
                'total_price' => 28000,
                'status' => 'diproses',
            ],
            [
                'customer_name' => 'Siti Nurhaliza',
                'item_name' => 'Nasi Goreng Seafood Spesial',
                'total_price' => 35000,
                'status' => 'pending',
            ],
            [
                'customer_name' => 'Rian Hidayat',
                'item_name' => 'Kopi Susu Gula Aren (Large)',
                'total_price' => 22000,
                'status' => 'selesai',
            ],
            [
                'customer_name' => 'Anisa Rahmawati',
                'item_name' => 'Mie Goreng Aceh Daging Sapi',
                'total_price' => 32000,
                'status' => 'pending',
            ],
            [
                'customer_name' => 'Bambang Sudiro',
                'item_name' => 'Es Cincau Hijau Segar',
                'total_price' => 15000,
                'status' => 'selesai',
            ],
        ];

        foreach ($sampleOrders as $order) {
            Pesanan::create($order);
        }
    }
}
