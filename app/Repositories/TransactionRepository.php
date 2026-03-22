<?php

namespace App\Repositories;

use App\Helpers\CodeTrxHelper;
use App\Interface\TransactionInterface;
use App\Models\Product;
use App\Models\Store;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Midtrans\Config;
use Midtrans\Snap;

class TransactionRepository implements TransactionInterface
{
    public function getAll(
        ?string $search,
        ?int $limit,
        bool $execute
    ) {
        $query = Transaction::where(function ($query) use ($search) {
            if ($search) {
                $query->search($search);
            }
        });

        if ($limit) {
            $query->take($limit);
        }

        if ($execute) {
            return $query->get();
        }

        return $query;

    }

    public function getAllPaginated(?string $search, ?int $row_per_page)
    {
        $query = $this->getAll($search, $row_per_page, false);

        return $query->paginate($row_per_page);

    }

    public function getById(?string $id)
    {
        return Transaction::find($id);
    }

    public function getByCode(?string $code)
    {
        return Transaction::where('code', $code)->first();
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $transaction = new Transaction;
            $transaction->code = CodeTrxHelper::generateTransactionCode();
            $transaction->store_id = $data['store_id'];
            $transaction->buyer_id = $data['buyer_id'];
            $transaction->address_id = $data['address_id'];
            $transaction->address = $data['address'];
            $transaction->city = $data['city'];
            $transaction->postal_code = $data['postal_code'];
            $transaction->shipping = $data['shipping'];
            $transaction->shipping_type = $data['shipping_type'];
            $transaction->shipping_cost = 0;
            $transaction->tax = 0;
            $transaction->grand_total = 0;
            $transaction->save();

            $transactionDetailRepository = new TransactionDetailRepository;

            $transactionDetails = [];
            foreach ($data['products'] as $product) {
                $productModel = Product::find($product['product_id']);

                $detail = $transactionDetailRepository->create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product['product_id'],
                    'qty' => $product['qty'],
                    'price' => $productModel->price,
                ]);

                $transactionDetails[] = $detail;
            }

            // 🔥 total subtotal
            $subtotal = collect($transactionDetails)->sum('subtotal');

            $weight = $this->getTotalWeight($transactionDetails);

            $calculation = $this->calculateShippingAndTax($data, $subtotal, $weight);

            $transaction->shipping_cost = $calculation['shipping_cost'];
            $transaction->tax = $calculation['tax'];
            $transaction->grand_total = $calculation['grand_total'];
            $transaction->save();

            // Set your Merchant Server Key
            Config::$serverKey = config('midtrans.serverKey');

            // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
            Config::$isProduction = config('midtrans.isProduction');

            // Set sanitization on (default)
            Config::$isSanitized = config('midtrans.isSanitized');

            // Set 3DS transaction for credit card to true
            Config::$is3ds = config('midtrans.is3ds');

            $params = [
                'transaction_details' => [
                    'order_id' => $transaction->code,
                    'gross_amount' => $transaction->grand_total,
                ],
                'customer_details' => [
                    'first_name' => $transaction->buyer->name,
                    'email' => $transaction->buyer->email,
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            $transaction->snap_token = $snapToken;
            $transaction->save();

            DB::commit();

            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function getTotalWeight(array $transactionDetails): int
    {
        $productIds = collect($transactionDetails)->pluck('product_id')->toArray();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $totalWeight = 0;

        foreach ($transactionDetails as $item) {
            $product = $products[$item['product_id']] ?? null;

            if ($product) {
                $totalWeight += $product->weight * $item['qty'];
            }
        }

        return $totalWeight;
    }

    private function calculateShippingAndTax(array $data, float $subtotal, int $weight): array
    {
        $origin = Store::find($data['store_id'])->address_id;
        $destination = $data['address_id'];

        $response = Http::asForm()->withHeaders([
            'key' => env('KEY_RAJA_ONGKIR'),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
            'origin' => $origin,
            'destination' => $destination,
            'weight' => $weight,
            'courier' => 'jne:sicepat:ide:sap:jnt:ninja:tiki:lion:anteraja:pos:ncs:rex:rpx:sentral:star:wahana:dse',
            'price' => 'lowest',
        ]);

        $result = $response->json();

        $shippingCost = 0;

        foreach ($result['data'] as $courier) {
            if (
                strtolower($courier['code']) === strtolower($data['shipping']) &&
                strtoupper($courier['service']) === strtoupper($data['shipping_type'])
            ) {
                $shippingCost = $courier['cost'];
                break;
            }
        }

        return [
            'shipping_cost' => round($shippingCost),
            'tax' => round($subtotal * 0.11),
            'grand_total' => round($subtotal * 1.11 + $shippingCost),
        ];
    }
}
