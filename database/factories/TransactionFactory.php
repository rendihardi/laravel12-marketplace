<?php

namespace Database\Factories;

use App\Models\Buyer;
use App\Models\Product;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $shippingTypes = ['regular', 'express', 'same_day'];
        $shippingType = $this->faker->randomElement($shippingTypes);

        // Generate shipping cost based on type
        $shippingCost = match ($shippingType) {
            'regular' => $this->faker->numberBetween(10000, 20000),
            'express' => $this->faker->numberBetween(20000, 30000),
            'same_day' => $this->faker->numberBetween(30000, 50000),
            default => $this->faker->numberBetween(10000, 20000),
        };

        return [
            'buyer_id' => Buyer::factory(),
            'store_id' => Store::factory(),
            'code' => 'TRX-'.$this->faker->unique()->numerify('##########'),
            'address_id' => fake()->numberBetween(1, 100),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'shipping' => fake()->randomElement(['JNT,', 'JNE', 'POS', 'TIKI']),
            'shipping_type' => $shippingType,
            'shipping_cost' => $shippingCost,
            'tracking_number' => fake()->randomNumber(8),
            'tax' => 0,
            'grand_total' => 0,
            'payment_status' => fake()->randomElement(['paid', 'unpaid']),
            'status' => fake()->randomElement(['unpaid', 'pending', 'processing', 'delivering', 'cancelled', 'completed']),
            'snap_token' => fake()->uuid(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Transaction $transaction) {
            // Create 1-5 transaction details for this transaction
            $numberOfDetails = $this->faker->numberBetween(1, 5);
            $subtotal = 0;

            for ($i = 0; $i < $numberOfDetails; $i++) {
                $product = Product::factory()->create(['store_id' => $transaction->store_id]);
                $qty = $this->faker->numberBetween(1, 5);
                $subtotal += $product->price * $qty;

                TransactionDetail::factory()->create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'qty' => $qty,
                    'subtotal' => $product->price * $qty,
                ]);
            }
            // calculate tax
            $tax = round($subtotal * 0.1);
            // calculate grand total
            $transaction->grand_total = $subtotal + $tax + $transaction->shipping_cost;
            $transaction->update([
                'tax' => $tax,
                'grand_total' => $transaction->grand_total,
            ]);
            $transaction->save();

        });
    }
}
