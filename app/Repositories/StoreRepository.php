<?php

namespace App\Repositories;

use App\Helpers\SlugHelper;
use App\Interface\StoreRepositoryInterface;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreRepository implements StoreRepositoryInterface
{
    public function getAll(?string $search, ?bool $isVerified, ?int $limit, bool $execute)
    {
        $query = Store::where(function ($query) use ($search, $isVerified) {
            if ($search) {
                $query->search($search);
                if ($isVerified) {
                    $query->where('is_verified', $isVerified);
                }
            }
        })->with('user');

        if ($limit) {
            $query->take($limit);
        }

        if ($execute) {
            return $query->get();
        }

        return $query;
    }

    public function getAllPaginated(?string $search, ?bool $isVerified, ?int $rowPerPage)
    {
        $query = $this->getAll($search, $isVerified, $rowPerPage, false);

        return $query->paginate($rowPerPage);

    }

    public function getById(?string $id)
    {
        return Store::find($id)->withCount('products', 'transactions')->with('user')->first();
    }

    public function getByUsername(?string $username)
    {
        $store = Store::where('username', $username)->withCount('products', 'transactions')->with('user')->first();

        return $store;
    }

    public function getByUser()
    {
        $user = Auth::user();
        $query = Store::where('user_id', $user->id)->withCount('products', 'transactions')->with('user')->first();

        return $query;
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $store = new Store;
            $store->name = $data['name'];
            $store->username = SlugHelper::createSlug(Store::class, $data['name'], 'slug');
            $store->user_id = $data['user_id'];
            $store->logo = $data['logo']->store('assets/store', 'public');
            $store->about = $data['about'];
            $store->phone = $data['phone'];
            $store->address_id = $data['address_id'];
            $store->address = $data['address'];
            $store->city = $data['city'];
            $store->postal_code = $data['postal_code'];
            if (isset($data['is_verified'])) {
                $store->is_verified = $data['is_verified'];
            }
            $store->save();
            $store->storeBalances()->create([
                'balance' => 0, ]);
            DB::commit();

            return $store;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(array $data, ?string $id)
    {
        DB::beginTransaction();
        try {
            $store = Store::find($id);
            if (isset($data['name']) && $data['name'] !== $store->name) {
                $store->name = $data['name'];
                $store->slug = SlugHelper::createSlug(Store::class, $data['name'], 'username');
            }
            $store->user_id = $data['user_id'];
            if (isset($data['logo'])) {
                $store->logo = $data['logo']->store('assets/store', 'public');
            }
            $store->about = $data['about'];
            $store->phone = $data['phone'];
            $store->address_id = $data['address_id'];
            $store->address = $data['address'];
            $store->city = $data['city'];
            $store->postal_code = $data['postal_code'];
            if (isset($data['is_verified'])) {
                $store->is_verified = $data['is_verified'];
            }
            $store->save();
            DB::commit();

            return $store;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(?string $id)
    {
        DB::beginTransaction();
        try {
            $store = Store::find($id);
            $store->delete();
            DB::commit();

            return $store;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateVerifiedStatus(string $id, ?bool $isVerified)
    {
        DB::beginTransaction();
        try {
            $store = Store::find($id);
            $store->is_verified = $isVerified;
            $store->save();
            DB::commit();

            return $store;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
