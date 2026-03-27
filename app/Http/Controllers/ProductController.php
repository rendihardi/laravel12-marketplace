<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\PaginatedResource;
use App\Http\Resources\ProductResource;
use App\Interface\ProductInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class ProductController extends Controller implements HasMiddleware
{
    private ProductInterface $productRepository;

    public function __construct(ProductInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public static function middleware()
    {
        return [
            // new Middleware(PermissionMiddleware::using(['product-list|product-create|product-edit|product-delete'])),
            new Middleware(PermissionMiddleware::using(['product-create']), only: ['store']),
            new Middleware(PermissionMiddleware::using(['product-edit']), only: ['update']),
            new Middleware(PermissionMiddleware::using(['product-delete']), only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string',
            'store_id' => 'nullable|string|exists:stores,id',
            'product_category_id' => 'nullable|string|exists:product_categories,id',
            'is_random' => 'nullable|boolean',
            'limit' => 'nullable|integer',
        ]);
        try {
            $products = $this->productRepository->getAll(
                $request->search,
                $request->store_id,
                $request->product_category_id,
                $request->is_random,
                $request->limit,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data Product', ProductResource::collection($products), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function getAllPaginated(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string',
            'store_id' => 'nullable|string|exists:stores,id',
            'product_category_id' => 'nullable|string|exists:product_categories,id',
            'is_Srandom' => 'nullable|boolean',
            'row_per_page' => 'required|integer',
        ]);

        try {
            $products = $this->productRepository->getAllPaginated(
                $request->search,
                $request->store_id,
                $request->product_category_id,
                $request->is_random,
                $request->row_per_page,
                false
            );

            return ResponseHelper::jsonResponse(true, 'Data Product', PaginatedResource::make($products, ProductResource::class), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request)
    {
        $request = $request->validated();
        try {
            $request = $this->productRepository->create($request);

            return ResponseHelper::jsonResponse(true, 'Data Product', new ProductResource($request), 201);
        } catch (\Throwable $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function getBySlug($slug)
    {
        try {
            $product = $this->productRepository->getBySlug($slug);
            if (! $product) {
                return ResponseHelper::jsonResponse(false, 'Data Product Not Found', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data Product', new ProductResource($product), 200);
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
            $product = $this->productRepository->getById($id);
            if (! $product) {
                return ResponseHelper::jsonResponse(false, 'Data Product Not Found', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data Product', new ProductResource($product), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, string $id)
    {
        $request = $request->validated();
        try {
            $product = $this->productRepository->getById($id);
            if (! $product) {
                return ResponseHelper::jsonResponse(false, 'Data Product Not Found', null, 404);
            }

            $product = $this->productRepository->update($request, $id);

            return ResponseHelper::jsonResponse(true, 'Data Product', new ProductResource($product), 200);
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
            $product = $this->productRepository->getById($id);
            if (! $product) {
                return ResponseHelper::jsonResponse(false, 'Data Product Not Found', null, 404);
            }

            $product = $this->productRepository->delete($id);

            return ResponseHelper::jsonResponse(true, 'Data Product Deleted', null, 200);
        } catch (\Throwable $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
