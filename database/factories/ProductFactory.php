<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'store_id' => Store::factory(),
            'product_category_id' => ProductCategory::inRandomOrder()->first()->id,
            'name' => $name,
            'slug' => Str::slug($name),
            'condition' => fake()->randomElement(['new', 'second']),
            'price' => fake()->randomFloat(2, 0, 100000000),
            'about' => fake()->paragraph(),
            'weight' => fake()->randomFloat(2, 0, 100000),
            'stock' => fake()->numberBetween(1, 999999999),
        ];

    }
}
