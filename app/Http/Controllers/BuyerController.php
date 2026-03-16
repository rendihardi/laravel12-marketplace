<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\BuyerStoreRequest;
use App\Http\Requests\BuyerUpdateRequest;
use App\Http\Resources\BuyerResource;
use App\Http\Resources\PaginatedResource;
use App\Interface\BuyerInterface;
use Illuminate\Http\Request;

class BuyerController extends Controller
{
    private BuyerInterface $buyerRepository;

    public function __construct(BuyerInterface $buyerRepository)
    {
        $this->buyerRepository = $buyerRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $buyers = $this->buyerRepository->getAll(
                $request->search,
                $request->limit,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data Buyer', BuyerResource::collection($buyers), 200);
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
            $buyers = $this->buyerRepository->getAllPaginated(
                $request->search,
                $request->row_per_page,
                false
            );

            return ResponseHelper::jsonResponse(true, 'Data Buyer', PaginatedResource::make($buyers, BuyerResource::class), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BuyerStoreRequest $request)
    {
        $request = $request->validated();
        try {
            $request = $this->buyerRepository->create($request);

            return ResponseHelper::jsonResponse(true, 'Data Buyer', new BuyerResource($request), 201);
        } catch (\Throwable $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $buyer = $this->buyerRepository->getById($id);
            if (! $buyer) {
                return ResponseHelper::jsonResponse(false, 'Data Buyer Not Found', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data buyer', new BuyerResource($buyer), 200);
        } catch (\Throwable $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BuyerUpdateRequest $request, string $id)
    {
        $request = $request->validated();
        try {
            $buyer = $this->buyerRepository->getById($id);
            if (! $buyer) {
                return ResponseHelper::jsonResponse(false, 'Data Buyer Not Found', null, 404);
            }

            $buyer = $this->buyerRepository->update($request, $id);

            return ResponseHelper::jsonResponse(true, 'Data buyer', new BuyerResource($buyer), 200);
        } catch (\Throwable $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $buyer = $this->buyerRepository->getById($id);
            if (! $buyer) {
                return ResponseHelper::jsonResponse(false, 'Data Buyer Not Found', null, 404);
            }

            $buyer = $this->buyerRepository->delete($id);

            return ResponseHelper::jsonResponse(true, 'Data Buyer Deleted', null, 200);
        } catch (\Throwable $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
