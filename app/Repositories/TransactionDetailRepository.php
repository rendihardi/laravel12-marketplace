<?php

namespace App\Repositories;

use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;

class TransactionDetailRepository
{
    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $transactionDetail = new TransactionDetail;
            $transactionDetail->transaction_id = $data['transaction_id'];
            $transactionDetail->product_id = $data['product_id'];
            $transactionDetail->qty = $data['qty'];
            $transactionDetail->price = $data['price'];
            $transactionDetail->subtotal = $data['qty'] * $data['price'];
            $transactionDetail->save();
            DB::commit();

            return $transactionDetail;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
