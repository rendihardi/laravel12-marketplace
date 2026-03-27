<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategoryResource extends JsonResource
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
            'image' => asset('storage/'.$this->image),
            'tagline' => $this->tagline,
            'description' => $this->description,
            'parent' => ProductCategoryResource::make($this->whenLoaded('parent')),
            'childrens' => ProductCategoryResource::collection($this->whenLoaded('childrens')),
            // 'product_count' => $this->when(isset($this->products_count), $this->products_count),
            // 'children_count' => $this->when(isset($this->childrens_count), $this->childrens_count),
            'product_count' => $this->whenCounted('products'),
            'children_count' => $this->whenCounted('childrens'),

        ];
    }
}
