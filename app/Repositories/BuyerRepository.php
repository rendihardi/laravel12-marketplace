<?php

namespace App\Repositories;

use App\Interface\BuyerInterface;
use App\Models\Buyer;
use Illuminate\Support\Facades\DB;

class BuyerRepository implements BuyerInterface
{
    public function getAll(
        ?string $search,
        ?int $limit,
        bool $execute
    ) {
        $query = Buyer::where(function ($query) use ($search) {
            if ($search) {
                $query->search($search);
            }
        });

        if ($limit && $limit > 0) {
            $query->take($limit);
        }

        if ($execute) {
            return $query->get();
        }

        return $query;

    }

    public function getAllPaginated(?string $search, ?int $row_per_page)
    {
        $query = $this->getAll($search, $row_per_page, false);

        return $query->paginate($row_per_page);

    }

    public function getById(?string $id)
    {
        return Buyer::find($id);
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $buyer = new Buyer;
            $buyer->user_id = $data['user_id'];
            $buyer->profile_picture = $data['profile_picture']->store('assets/buyer', 'public');
            $buyer->phone_number = $data['phone_number'];
            $buyer->save();
            DB::commit();

            return $buyer;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(array $data, ?string $id)
    {
        DB::beginTransaction();
        try {
            $buyer = Buyer::find($id);
            if (isset($data['profile_picture'])) {
                $buyer->logo = $data['profile_picture']->store('assets/buyer', 'public');
            }
            $buyer->phone_number = $data['phone_number'];
            $buyer->save();
            DB::commit();

            return $buyer;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(?string $id)
    {
        DB::beginTransaction();
        try {
            $buyer = Buyer::find($id);
            $buyer->delete();
            DB::commit();

            return $buyer;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
