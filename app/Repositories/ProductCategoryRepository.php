<?php

namespace App\Repositories;

use App\Helpers\SlugHelper;
use App\Interface\ProductCategoryInterface;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductCategoryRepository implements ProductCategoryInterface
{
    public function getAll(
        ?string $search,
        ?bool $isParent,
        ?int $limit,
        bool $execute
    ) {
        $query = ProductCategory::where(function ($query) use ($search, $isParent) {
            if ($search) {
                $query->search($search);
            }
            if ($isParent) {
                $query->whereNull('parent_id');
            }
        })->withCount(['childrens', 'products'])->with('childrens');

        if ($limit && $limit > 0) {
            $query->take($limit);
        }

        if ($execute) {
            $cacheKey = "categories.all.search_{$search}.isParent_{$isParent}.limit_{$limit}";
            $cacheDuration = now()->addMinutes(60);

            return Cache::remember($cacheKey, $cacheDuration, function () use ($query) {
                return $query->get();
            });
        }

        // if ($execute) {
        //     $cacheKey = 'categories.all.search_'.($search ?? 'null')
        //         .'.isParent_'.($isParent ?? 'null')
        //         .'.limit_'.($limit ?? 'null');

        //     return Cache::tags('categories')->remember($cacheKey, 60, function () use ($query) {
        //         return $query->get();
        //     });
        // }

        return $query;

    }

    public function getAllPaginated(?string $search, ?bool $isParent, ?int $row_per_page)
    {
        $query = $this->getAll($search, $isParent = false, $row_per_page, false);

        return $query->paginate($row_per_page);

    }

    public function getById(?string $id)
    {
        return ProductCategory::withCount('products')
            ->with('childrens')
            ->where('id', $id)
            ->first();
    }

    public function getBySlug(?string $slug)
    {
        $query = ProductCategory::where('slug', $slug)->withCount('products')->with('childrens');

        return $query->first();
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $productCategory = new ProductCategory;
            if (isset($data['parent_id'])) {
                $productCategory->parent_id = $data['parent_id'];
            }
            // $productCategory->parent_id = $data['parent_id'];
            $productCategory->name = $data['name'];
            $productCategory->slug = SlugHelper::createSlug(ProductCategory::class, $data['name'], 'slug');
            $productCategory->tagline = $data['tagline'];
            $productCategory->description = $data['description'];
            $productCategory->image = $data['image']->store('assets/category', 'public');
            $productCategory->save();
            DB::commit();

            return $productCategory;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(array $data, ?string $id)
    {
        DB::beginTransaction();
        try {
            $productCategory = ProductCategory::find($id);
            if (isset($data['parent_id'])) {
                $productCategory->parent_id = $data['parent_id'];
            }
            if (isset($data['name']) && $data['name'] !== $productCategory->name) {
                $productCategory->name = $data['name'];
                $productCategory->slug = SlugHelper::createSlug(
                    ProductCategory::class,
                    $data['name'],
                    'slug'
                );
            }
            // $productCategory->slug = $data['slug'];
            $productCategory->tagline = $data['tagline'];
            $productCategory->description = $data['description'];
            if (isset($data['image'])) {
                $productCategory->image = $data['image']->store('assets/category', 'public');
            }

            $productCategory->save();
            DB::commit();

            return $productCategory;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(?string $id)
    {
        DB::beginTransaction();
        try {
            $productCategory = ProductCategory::find($id);
            $productCategory->delete();
            DB::commit();

            return $productCategory;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
