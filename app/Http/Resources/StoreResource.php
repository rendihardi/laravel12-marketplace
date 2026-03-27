<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
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
            'user' => new UserResource($this->whenLoaded('user')),
            'username' => $this->username,
            'logo' => asset('storage/'.$this->logo),
            'about' => $this->about,
            'phone' => $this->phone,
            'address_id' => $this->address_id,
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'is_verified' => $this->is_verified,
            // 'product_count' => $this->when(isset($this->products_count), $this->products_count),
            // 'transaction_count' => $this->when(isset($this->transaction_count), $this->transaction_count),
            'product_count' => $this->whenCounted('products'),
            'transaction_count' => $this->whenCounted('transactions'),
            // 'transaction_count' => $this->transactions->count()
        ];
    }
}
