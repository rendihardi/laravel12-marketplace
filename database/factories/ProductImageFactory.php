<?php

namespace Database\Factories;

use App\Helpers\ImageHelper\ImageHelper;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductImage>
 */
class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $imageHelper = new ImageHelper;

        return [
            'product_id' => Product::factory(),
            'image' => $imageHelper->storeAndResizeImage(
                $imageHelper->createDummyImageWithTextSizeAndPosition(
                    250,
                    250,
                    'center',
                    'center',
                    'random',
                    'medium'
                ),
                'product',
                250,
                250
            ),
            'is_thumbnail' => false,
        ];
    }

    public function thumbnail(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_thumbnail' => true,
            ];
        });
    }
}
