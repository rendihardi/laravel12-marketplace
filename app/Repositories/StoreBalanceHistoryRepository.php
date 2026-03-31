<?php

namespace App\Repositories;

use App\Interface\StoreBalanceHistoryInterface;
use App\Models\StoreBalanceHistory;
use Illuminate\Support\Facades\DB;

class StoreBalanceHistoryRepository implements StoreBalanceHistoryInterface
{
    public function getAll(
        ?string $search,
        ?int $limit,
        bool $execute
    ) {
        $query = StoreBalanceHistory::where(function ($query) use ($search) {
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
        return StoreBalanceHistory::find($id);
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $storeBalanceHistory = new StoreBalanceHistory;
            $storeBalanceHistory->store_balance_id = $data['store_balance_id'];
            $storeBalanceHistory->type = $data['type'];
            $storeBalanceHistory->reference_id = $data['reference_id'];
            $storeBalanceHistory->reference_type = $data['reference_type'];
            $storeBalanceHistory->amount = $data['amount'];
            $storeBalanceHistory->remarks = $data['remarks'];
            $storeBalanceHistory->save();
            DB::commit();

            return $storeBalanceHistory;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(array $data, ?string $id)
    {
        DB::beginTransaction();
        try {
            $storeBalanceHistory = StoreBalanceHistory::find($id);
            $storeBalanceHistory->store_balance_id = $data['store_balance_id'];
            $storeBalanceHistory->type = $data['type'];
            $storeBalanceHistory->reference_id = $data['reference_id'];
            $storeBalanceHistory->reference_type = $data['reference_type'];
            $storeBalanceHistory->amount = $data['amount'];
            $storeBalanceHistory->remarks = $data['remarks'];
            $storeBalanceHistory->save();
            DB::commit();

            return $storeBalanceHistory;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
