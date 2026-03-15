<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\StoreBalance;
use App\Models\StoreBalanceHistory;
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
        Store::factory()->count(15)->create()->each(function ($store) {
            $storeBalance = StoreBalance::factory()->create([
                'store_id' => $store->id,
            ]);
            $storeBalaceHistory = StoreBalanceHistory::factory()->create([
                'store_balance_id' => $storeBalance->id,
                'amount' => $storeBalance->balance,
            ]);

        });
    }
}
