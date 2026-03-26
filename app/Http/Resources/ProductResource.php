<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'product_images' => ProductImageResource::collection($this->whenLoaded('productImages')),
            'condition' => $this->condition,
            'price' => $this->price,
            'stock' => $this->stock,
            'weight' => $this->weight,
            'description' => $this->about,
            'product_category' => new ProductCategoryResource($this->productCategory),
            'store' => new StoreResource($this->store),
            'product_reviews' => ProductReviewResource::collection($this->whenLoaded('productReviews')),
        ];
    }
}
