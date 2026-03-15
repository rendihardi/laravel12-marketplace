<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Resources\PaginatedResource;
use App\Http\Resources\StoreBalanceResource;
use App\Interface\StoreBalanceInterface;
use Illuminate\Http\Request;

class StoreBalanceController extends Controller
{
    private StoreBalanceInterface $storeBalanceRepository;

    public function __construct(StoreBalanceInterface $storeBalanceRepository)
    {
        $this->storeBalanceRepository = $storeBalanceRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $balance = $this->storeBalanceRepository->getAllPaginated(
                $request->search,
                $request->limit,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data User', StoreBalanceResource::collection($balance), 200);
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
            $balance = $this->storeBalanceRepository->getAllPaginated(
                $request->search,
                $request->row_per_page,
                false
            );

            return ResponseHelper::jsonResponse(true, 'Data User', PaginatedResource::make($balance, StoreBalanceResource::class), 200);
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
            $balance = $this->storeBalanceRepository->getById($id);
            if (! $balance) {
                return ResponseHelper::jsonResponse(false, 'Data Balance Store Not Found', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data Balance Store', new StoreBalanceResource($balance), 200);
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
