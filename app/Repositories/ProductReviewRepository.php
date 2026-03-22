<?php

namespace App\Repositories;

use App\Interface\ProductReviewInterface;
use App\Models\ProductReview;
use Illuminate\Support\Facades\DB;

class ProductReviewRepository implements ProductReviewInterface
{
    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $productReview = new ProductReview;
            $productReview->transaction_id = $data['transaction_id'];
            // $productReview->transaction_detail_id = $data['transaction_detail_id'];
            // $productReview->store_id = $data['store_id'];
            // $productReview->product_id = $data['product_id'];
            // $productReview->buyer_id = $data['buyer_id'];
            $productReview->rating = $data['rating'];
            $productReview->comment = $data['comment'];
            $productReview->save();
            DB::commit();

            return $productReview;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
