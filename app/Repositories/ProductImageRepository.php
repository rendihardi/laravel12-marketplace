<?php

namespace App\Repositories;

use App\Interface\ProductImageInterface;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;

class ProductImageRepository implements ProductImageInterface
{
    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $productImage = new ProductImage;
            $productImage->product_id = $data['product_id'];
            $productImage->image = $data['image']->store('assets/product', 'public');
            $productImage->is_thumbnail = $data['is_thumbnail'];
            $productImage->save();
            DB::commit();

            return $productImage;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(?string $id)
    {
        DB::beginTransaction();
        try {
            $productImage = ProductImage::find($id);
            $productImage->delete();
            DB::commit();

            return $productImage;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
