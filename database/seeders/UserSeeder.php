<?php

namespace Database\Seeders;

use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        $admin = UserFactory::new()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
        ])->assignRole('admin');

        // Seller
        $seller = UserFactory::new()->create([
            'name' => 'Seller Contoh',
            'email' => 'seller@gmail.com',
            'password' => bcrypt('password'),
        ])->assignRole('store');

        // Buyer
        $buyer = UserFactory::new()->create([
            'name' => 'Buyer Contoh',
            'email' => 'buyer@gmail.com',
            'password' => bcrypt('password'),
        ])->assignRole('buyer');

        UserFactory::new()->count(15)->create();
    }
}
