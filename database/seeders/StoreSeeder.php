<?php

namespace Database\Seeders;

use App\Models\Store;
use Database\Factories\StoreFactory;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // StoreFactory::new()->count(15)->create();
        Store::factory()->count(15)->create();
    }
}
