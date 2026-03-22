<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\ProductReviewStoreRequest;
use App\Http\Resources\ProductReviewResource;
use App\Interface\ProductReviewInterface;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    private ProductReviewInterface $productReviewRepository;

    public function __construct(ProductReviewInterface $productReviewRepository)
    {
        $this->productReviewRepository = $productReviewRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductReviewStoreRequest $request)
    {
        $request = $request->validated();
        try {
            $request = $this->productReviewRepository->create($request);

            return ResponseHelper::jsonResponse(true, 'Data Product Review', new ProductReviewResource($request), 201);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
