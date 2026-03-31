<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'buyer' => new BuyerResource($this->buyer),
            'store' => new StoreResource($this->store),
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'shipping' => $this->shipping,
            'shipping_type' => $this->shipping_type,
            'shipping_cost' => (float) (string) $this->shipping_cost,
            'tracking_number' => $this->tracking_number,
            'tax' => (float) (string) $this->tax,
            'grand_total' => (float) (string) $this->grand_total,
            'payment_status' => $this->payment_status,
            'delivery_status' => $this->status,
            'delivery_proof' => $this->delivery_proof,
            'snap_token' => $this->snap_token,
            'created_at' => $this->created_at,
            'transaction_detail' => TransactionDetailResource::collection($this->transactionDetails),
        ];
    }
}
