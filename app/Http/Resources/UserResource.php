<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $role = $this->roles->first()->name ?? '-';

        // return [
        //     'id' => $this->id,
        //     'name' => $this->name,
        //     'email' => $this->email,
        //     'role' => $this->roles->first()->name,
        //     'permissions' => $this->permissions,
        //     'token' => $this->token,
        //     'store' => $role === 'store' ? $this->store : null,
        //     'buyer' => $role === 'buyer' ? $this->buyer : null,
        //     // 'roles' => $this->roles->pluck('name'), multi role
        // ];

        // $role = $this->whenLoaded('roles', function () {
        //     return $this->roles->first()?->name ?? '-';
        // }, '-');

        return [
            'id' => $this->id,
            'profile_picture' => asset('storage/'.$this->profile_picture),
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->roles->first()?->name ?? '-',
            'permissions' => $this->whenLoaded('permissions'),
            'token' => $this->when(isset($this->token), $this->token),
            'store' => $this->when(
                $role === 'store',
                $this->whenLoaded('store', fn () => new StoreResource($this->store))
            ),
            'buyer' => $this->when(
                $role === 'buyer',
                $this->whenLoaded('buyer', fn () => new BuyerResource($this->buyer))
            ),
        ];
    }
}
