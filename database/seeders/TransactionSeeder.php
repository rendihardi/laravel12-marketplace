<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sellerStore = \App\Models\Store::where('name', 'Toko Seller Contoh')->first();
        if ($sellerStore) {
            Transaction::factory()->count(15)->create([
                'store_id' => $sellerStore->id
            ]);
        }

        Transaction::factory()->count(85)->create();
    }
}
