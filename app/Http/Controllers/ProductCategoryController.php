<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\ProductCategoryStoreRequest;
use App\Http\Requests\ProductCategoryUpdateRequest;
use App\Http\Resources\PaginatedResource;
use App\Http\Resources\ProductCategoryResource;
use App\Interface\ProductCategoryInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class ProductCategoryController extends Controller implements HasMiddleware
{
    private ProductCategoryInterface $productCategoryRepository;

    public function __construct(ProductCategoryInterface $productCategoryRepository)
    {
        $this->productCategoryRepository = $productCategoryRepository;
    }

    public static function middleware()
    {
        return [
            new Middleware(PermissionMiddleware::using(['product-category-list|product-category-create|product-category-edit|product-category-delete'])),
            new Middleware(PermissionMiddleware::using(['product-category-create']), only: ['store']),
            new Middleware(PermissionMiddleware::using(['product-category-edit']), only: ['update']),
            new Middleware(PermissionMiddleware::using(['product-category-delete']), only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string',
            'is_parent' => 'nullable|boolean',
            'limit' => 'nullable|integer',
        ]);

        try {
            $categories = $this->productCategoryRepository->getAll(
                $request->search,
                $request->is_parent,
                $request->limit,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data Category', ProductCategoryResource::collection($categories), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function getAllPaginated(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string',
            'is_parent' => 'nullable|boolean',
            'row_per_page' => 'required|integer',
        ]);
        try {
            $categories = $this->productCategoryRepository->getAllPaginated(
                $request->search,
                $request->is_parent,
                $request->row_per_page,
                false
            );

            return ResponseHelper::jsonResponse(true, 'Data Category', PaginatedResource::make($categories, ProductCategoryResource::class), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductCategoryStoreRequest $request)
    {
        $request = $request->validated();
        try {
            $request = $this->productCategoryRepository->create($request);

            return ResponseHelper::jsonResponse(true, 'Data Category', new ProductCategoryResource($request), 201);
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
            $category = $this->productCategoryRepository->getById($id);
            if (! $category) {
                return ResponseHelper::jsonResponse(false, 'Data Category Not Found', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data Category', new ProductCategoryResource($category), 200);
        } catch (\Throwable $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function getBySlug(string $slug)
    {
        try {
            $category = $this->productCategoryRepository->getBySlug($slug);
            if (! $category) {
                return ResponseHelper::jsonResponse(false, 'Data Category Not Found', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data Category', new ProductCategoryResource($category), 200);
        } catch (\Throwable $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductCategoryUpdateRequest $request, string $id)
    {
        $request = $request->validated();
        try {
            $category = $this->productCategoryRepository->getById($id);
            if (! $category) {
                return ResponseHelper::jsonResponse(false, 'Data Category Not Found', null, 404);
            }

            $category = $this->productCategoryRepository->update($request, $id);

            return ResponseHelper::jsonResponse(true, 'Data Category', new ProductCategoryResource($category), 200);
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
            $category = $this->productCategoryRepository->getById($id);
            if (! $category) {
                return ResponseHelper::jsonResponse(false, 'Data Category Not Found', null, 404);
            }

            $category = $this->productCategoryRepository->delete($id);

            return ResponseHelper::jsonResponse(true, 'Data Category Deleted', null, 200);
        } catch (\Throwable $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
