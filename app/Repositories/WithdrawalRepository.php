<?php

namespace App\Repositories;

use App\Interface\WithdrawalInterface;
use App\Models\Withdrawal;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class WithdrawalRepository implements WithdrawalInterface
{
    public function getAll(
        ?string $search,
        ?int $limit,
        bool $execute
    ) {
        $query = Withdrawal::where(function ($query) use ($search) {
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
        return Withdrawal::find($id);
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $withdrawal = new Withdrawal;
            $withdrawal->store_balance_id = $data['store_balance_id'];
            $withdrawal->amount = $data['amount'];
            $withdrawal->bank_name = $data['bank_name'];
            $withdrawal->bank_account_name = $data['bank_account_name'];
            $withdrawal->bank_account_number = $data['bank_account_number'];

            $withdrawal->save();
            $storeBalanceRepository = new StoreBalanceRepository;
            $storeBalanceRepository->debit($withdrawal->store_balance_id, $withdrawal->amount);

            $storeBalanceRepository = new StoreBalanceHistoryRepository;
            $storeBalanceRepository->create([
                'store_balance_id' => $withdrawal->store_balance_id,
                'type' => 'withdraw',
                'reference_id' => $withdrawal->id,
                'reference_type' => Withdrawal::class,
                'amount' => $withdrawal->amount,
                'remarks' => "Permintaan penarikan dana ke {$withdrawal->bank_name} - {$withdrawal->bank_account_number}",
            ]);
            DB::commit();

            return $withdrawal;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function approve(?string $id, ?UploadedFile $proof)
    {
        DB::beginTransaction();
        try {
            $withdrawal = Withdrawal::find($id);
            $withdrawal->proof = $proof->store('assets/withdrawal', 'public');
            $withdrawal->status = 'approved';
            $withdrawal->save();

            // $storeBalanceRepository = new StoreBalanceRepository;
            // $storeBalanceRepository->credit($withdrawal->store_balance_id, $withdrawal->amount);

            $storeBalanceRepository = new StoreBalanceHistoryRepository;
            $storeBalanceRepository->create([
                'store_balance_id' => $withdrawal->store_balance_id,
                'type' => 'withdraw',
                'reference_id' => $withdrawal->id,
                'reference_type' => Withdrawal::class,
                'amount' => $withdrawal->amount,
                'remarks' => "Penarikan dana ke {$withdrawal->bank_name} - {$withdrawal->bank_account_number} disetujui",
            ]);
            DB::commit();

            return $withdrawal;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
