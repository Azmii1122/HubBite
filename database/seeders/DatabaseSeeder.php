<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            [
                'username' => 'Super Admin HubBite',
                'email' => 'admin@hubbite.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ],
            [
                'username' => 'Dafka Kitchen (Merchant)',
                'email' => 'merchant@hubbite.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_MERCHANT,
            ],
            [
                'username' => 'Warung Geprek Kampus',
                'email' => 'geprek@hubbite.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_MERCHANT,
            ],
            [
                'username' => 'Budi Sudarsono',
                'email' => 'buyer@hubbite.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_BUYER,
            ],
            [
                'username' => 'Siti Nurhaliza',
                'email' => 'siti@hubbite.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_BUYER,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->call(HubBiteSeeder::class);
    }
}
