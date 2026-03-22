<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\TransactionStoreRequest;
use App\Http\Requests\TransactionUpdateRequest;
use App\Http\Resources\PaginatedResource;
use App\Http\Resources\TransactionResource;
use App\Interface\TransactionInterface;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    private TransactionInterface $transactionRepository;

    public function __construct(TransactionInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $transactions = $this->transactionRepository->getAll(
                $request->search,
                $request->limit,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data Buyer', TransactionResource::collection($transactions), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function getAllPaginated(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string',
            'row_per_page' => 'required|integer',
        ]);

        try {
            $transactions = $this->transactionRepository->getAllPaginated(
                $request->search,
                $request->row_per_page,
                false
            );

            return ResponseHelper::jsonResponse(true, 'Data Transaksi', PaginatedResource::make($transactions, TransactionResource::class), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $transaction = $this->transactionRepository->getById($id);

            return ResponseHelper::jsonResponse(true, 'Data Transaksi', TransactionResource::make($transaction), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function getByCode(string $code)
    {
        try {
            $transaction = $this->transactionRepository->getByCode($code);

            return ResponseHelper::jsonResponse(true, 'Data Transaksi', TransactionResource::make($transaction), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionStoreRequest $request)
    {
        $request = $request->validated();
        try {
            $transaction = $this->transactionRepository->create($request);

            return ResponseHelper::jsonResponse(true, 'Data Transaksi', TransactionResource::make($transaction), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TransactionUpdateRequest $request, string $id)
    {
        $request = $request->validated();
        try {
            $transaction = $this->transactionRepository->getById($id);
            if (! $transaction) {
                return ResponseHelper::jsonResponse(false, 'Data Transaksi Not Found', null, 404);
            }
            $transaction = $this->transactionRepository->updateStatus($id, $request);

            return ResponseHelper::jsonResponse(true, 'Data Transaksi', TransactionResource::make($transaction), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $transaction = $this->transactionRepository->getById($id);
            if (! $transaction) {
                return ResponseHelper::jsonResponse(false, 'Data Transaksi Not Found', null, 404);
            }
            $transaction = $this->transactionRepository->delete($id);

            return ResponseHelper::jsonResponse(true, 'Data Transaksi', TransactionResource::make($transaction), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
