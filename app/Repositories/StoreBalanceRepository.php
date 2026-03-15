<?php

namespace App\Repositories;

use App\Interface\StoreBalanceInterface;
use App\Models\StoreBalance;
use Illuminate\Support\Facades\DB;

class StoreBalanceRepository implements StoreBalanceInterface
{
    public function getAll(
        ?string $search,
        ?int $limit,
        bool $execute
    ) {
        $query = StoreBalance::where(function ($query) use ($search) {
            if ($search) {
                $query->search($search);
            }
        });

        if ($limit) {
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
        return StoreBalance::find($id);
    }

    public function credit(?string $id, ?string $amount)
    {
        DB::beginTransaction();
        try {
            $storeBalance = StoreBalance::find($id);
            $storeBalance->balance = bcadd($storeBalance->balance, $amount, 2);
            $storeBalance->save();
            DB::commit();

            return $storeBalance;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function debit(?string $id, ?string $amount)
    {
        DB::beginTransaction();
        try {
            $storeBalance = StoreBalance::find($id);
            if (bccomp($storeBalance->balance, $amount, 2) < 0) {
                throw new \Exception('Insufficient balance');
            }
            $storeBalance->balance = bcsub($storeBalance->balance, $amount, 2);
            $storeBalance->save();
            DB::commit();

            return $storeBalance;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
