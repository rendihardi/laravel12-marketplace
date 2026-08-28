<?php

namespace Database\Seeders;

use App\Models\Buyer;
use App\Models\User;
use Illuminate\Database\Seeder;

class BuyerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buyerUser = User::where('email', 'buyer@gmail.com')->first();
        if ($buyerUser) {
            Buyer::factory()->create([
                'user_id' => $buyerUser->id,
            ]);
        }

        Buyer::factory()->count(15)->create();
    }
}
