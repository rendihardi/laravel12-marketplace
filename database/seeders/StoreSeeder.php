<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\StoreBalance;
use App\Models\StoreBalanceHistory;
use App\Models\Withdrawal;
use Database\Factories\StoreFactory;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sellerUser = \App\Models\User::where('email', 'seller@gmail.com')->first();
        if ($sellerUser) {
            $customStore = Store::factory()->create([
                'user_id' => $sellerUser->id,
                'name' => 'Toko Seller Contoh',
                'address_id' => 64907,
                'is_verified' => true,
            ]);
            $this->createBalances($customStore);
        }

        Store::factory()->count(15)->create()->each(function ($store) {
            $this->createBalances($store);
        });
    }

    private function createBalances($store) {
        $storeBalance = StoreBalance::factory()->create([
            'store_id' => $store->id,
        ]);
        StoreBalanceHistory::factory()->create([
            'store_balance_id' => $storeBalance->id,
            'amount' => $storeBalance->balance,
        ]);
        Withdrawal::factory()->count(1)->create([
            'store_balance_id' => $storeBalance->id,
        ]);
    }
}
