<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Resources\PaginatedResource;
use App\Http\Resources\StoreBalanceHistoryResource;
use App\Interface\StoreBalanceHistoryInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class StoreBalanceHistoryController extends Controller implements HasMiddleware
{
    private StoreBalanceHistoryInterface $storeBalanceHistoryRepository;

    public function __construct(StoreBalanceHistoryInterface $storeBalanceHistoryRepository)
    {
        $this->storeBalanceHistoryRepository = $storeBalanceHistoryRepository;
    }

    public static function middleware()
    {
        return [
            new Middleware(PermissionMiddleware::using(['store-balance-history-list|store-balance-history-create|store-balance-history-edit|store-balance-history-delete']), only: ['index', 'getAllPaginate', 'show']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $balanceHistory = $this->storeBalanceHistoryRepository->getAll(
                $request->search,
                $request->limit,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data User', StoreBalanceHistoryResource::collection($balanceHistory), 200);
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
            $balanceHistory = $this->storeBalanceHistoryRepository->getAllPaginated(
                $request->search,
                $request->row_per_page,
                false
            );

            return ResponseHelper::jsonResponse(true, 'Data User', PaginatedResource::make($balanceHistory, StoreBalanceHistoryResource::class), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $balance = $this->storeBalanceHistoryRepository->getById($id);
            if (! $balance) {
                return ResponseHelper::jsonResponse(false, 'Data Balance Store Not Found', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data Balance Store', new StoreBalanceHistoryResource($balance), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
