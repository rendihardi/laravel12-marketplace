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
        $query = Transaction::latest();

        $user = auth('sanctum')->user();
        if ($user) {
            $role = $user->roles->first()?->name;
            if ($role === 'buyer') {
                $query->where('buyer_id', $user->buyer?->id);
            } elseif ($role === 'store') {
                $query->where('store_id', $user->store?->id);
            }
        }

        $query->where(function ($query) use ($search) {
            if ($search) {
                $query->search($search);
            }
        });

        if ($limit && $limit > 0) {
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
        return Transaction::with([
            'buyer.user',
            'store.user',
            'transactionDetails.product.productCategory',
            'transactionDetails.product.productImages',
        ])->find($id);
    }

    public function getByCode(?string $code)
    {
        return Transaction::with([
            'buyer.user',
            'store.user',
            'transactionDetails.product.productCategory',
            'transactionDetails.product.productImages',
        ])->where('code', $code)->first();
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
                    'first_name' => $transaction->buyer->user->name ?? 'Guest',
                    'email' => $transaction->buyer->user->email ?? 'guest@example.com',
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

        \Illuminate\Support\Facades\Log::info("RajaOngkir Request - origin: {$origin}, destination: {$destination}, weight: {$weight}");

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

        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $courier) {
                if (
                    strtolower($courier['code']) === strtolower($data['shipping']) &&
                    strtoupper($courier['service']) === strtoupper($data['shipping_type'])
                ) {
                    $shippingCost = $courier['cost'];
                    break;
                }
            }
        } else {
            \Illuminate\Support\Facades\Log::error("RajaOngkir/Komerce API error response. Origin: {$origin}, Destination: {$destination}, Response: " . json_encode($result));
            throw new \Exception('Failed to calculate shipping cost. The shipping destination or origin address might be invalid.');
        }

        return [
            'shipping_cost' => round($shippingCost),
            'tax' => round($subtotal * 0.11),
            'grand_total' => round($subtotal * 1.11 + $shippingCost),
        ];
    }

    public function updateStatus(?string $id, array $data)
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::find($id);
            if (isset($data['tracking_number'])) {
                $transaction->tracking_number = $data['tracking_number'];
            }
            if (isset($data['delivery_proof'])) {
                $transaction->delivery_proof = $data['delivery_proof']->store('assets/transaction', 'public');
            }
            $oldStatus = $transaction->status;
            $transaction->status = $data['status'];
            $transaction->save();

            if ($data['status'] === 'completed' && $oldStatus !== 'completed') {
                $storeBalance = \App\Models\StoreBalance::where('store_id', $transaction->store_id)->first();
                if ($storeBalance) {
                    $storeBalanceRepository = new \App\Repositories\StoreBalanceRepository;
                    $storeBalanceRepository->credit($storeBalance->id, $transaction->grand_total);

                    $storeBalanceHistoryRepository = new \App\Repositories\StoreBalanceHistoryRepository;
                    $storeBalanceHistoryRepository->create([
                        'store_balance_id' => $storeBalance->id,
                        'type' => 'income',
                        'reference_id' => $transaction->id,
                        'reference_type' => Transaction::class,
                        'amount' => $transaction->grand_total,
                        'remarks' => "Pendapatan dari transaksi {$transaction->code}",
                    ]);
                }
            }

            DB::commit();

            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(?string $id)
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::find($id);
            $transaction->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
