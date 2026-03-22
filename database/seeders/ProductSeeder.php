<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory()->count(25)->create()->each(function ($product) {
            // Create 1-5 images for each product
            $imageCount = rand(1, 5);

            // Create the first image as thumbnail
            ProductImage::factory()->thumbnail()->create([
                'product_id' => $product->id,
            ]);

            // Create additional images if needed
            if ($imageCount > 1) {
                ProductImage::factory()->count($imageCount - 1)->create([
                    'product_id' => $product->id,
                ]);
            }
        });
    }
}
