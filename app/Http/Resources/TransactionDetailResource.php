<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionDetailResource extends JsonResource
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
            // 'transaction' => new TransactionResource($this->transaction),
            // 'product' => new ProductResource($this->product),
            'quantity' => $this->quantity,
            'price' => $this->price,
            'subtotal' => (float) (string) $this->subtotal,
        ];
    }
}
