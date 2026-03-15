<?php

namespace Database\Factories;

use App\Helpers\ImageHelper\ImageHelper;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Store>
 */
class StoreFactory extends Factory
{
    protected $model = Store::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $imageHelper = new ImageHelper;

        return [
            'user_id' => User::factory(),
            'name' => fake()->company(),
            'logo' => $imageHelper->storeAndResizeImage(
                $imageHelper->createDummyImageWithTextSizeAndPosition(
                    250,
                    250,
                    'center',
                    'center',
                    'random',
                    'medium'
                ),
                'store',
                250,
                250
            ),
            'about' => fake()->paragraph(),
            'phone' => fake()->phoneNumber(),
            'address_id' => fake()->numberBetween(1, 100),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'is_verified' => fake()->boolean(70),
        ];
    }
}
