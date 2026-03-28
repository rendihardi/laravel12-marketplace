<?php

namespace App\Repositories;

use App\Helpers\SlugHelper;
use App\Interface\ProductInterface;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductRepository implements ProductInterface
{
    public function getAll(
        ?string $search,
        ?string $storeId,
        ?string $productCategoryid,
        ?bool $random,
        ?int $limit,
        bool $execute
    ) {
        // $query = Product::where(function ($query) use ($search, $storeId, $productCategoryid) {
        //     if ($search) {
        //         $query->search($search);
        //     }
        //     if ($storeId) {
        //         $query->where('store_id', $storeId);
        //     }
        //     if ($productCategoryid) {
        //         $query->where('product_category_id', $productCategoryid);
        //     }
        // })
        //     ->select('products.*')
        //     ->with(['productImages', 'productCategory']);

        // // 🔥 kalau ada salah satu parameter
        // if ($productCategoryid || $storeId) {

        //     $query->addSelect([
        //         'product_count' => Product::selectRaw('count(*)')
        //             ->from('products as p2')
        //             ->where(function ($q) use ($productCategoryid, $storeId) {

        //                 // kalau filter category
        //                 if ($productCategoryid) {
        //                     $q->whereColumn('p2.product_category_id', 'products.product_category_id');
        //                 }

        //                 // kalau filter store
        //                 if ($storeId) {
        //                     $q->whereColumn('p2.store_id', 'products.store_id');
        //                 }

        //             }),
        //     ]);
        // }
        $query = Product::where(function ($query) use ($search, $storeId, $productCategoryid) {
            if ($search) {
                $query->search($search);
            }
            if ($storeId) {
                $query->where('store_id', $storeId);
            }
            if ($productCategoryid) {
                $query->where('product_category_id', $productCategoryid);
            }

        })->with([
            'productCategory' => function ($q) {
                $q->withCount('products');
            },
            'productImages',
        ]);

        if ($limit !== 0) {
            $query->take($limit);
        }

        if ($execute) {
            return $query->get();
        }

        if ($random) {
            $query->inRandomOrder();
        }

        return $query;

    }

    public function getAllPaginated(?string $search, ?string $storeId, ?string $productCategoryid, ?bool $random, ?int $row_per_page)
    {
        $query = $this->getAll($search, $storeId, $productCategoryid, $random, $row_per_page, false);

        return $query->paginate($row_per_page);

    }

    public function getById(?string $id)
    {
        return Product::with(['productImages', 'productCategory', 'productReviews', 'store'])->find($id);
    }

    public function getBySlug(?string $slug)
    {
        return Product::with(['productImages', 'productCategory', 'productReviews', 'store'])->where('slug', $slug)->first();
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $product = new Product;
            $product->store_id = $data['store_id'];
            $product->product_category_id = $data['product_category_id'];
            $product->name = $data['name'];
            $product->slug = SlugHelper::createSlug(Product::class, $data['name'], 'slug');
            $product->about = $data['about'];
            $product->condition = $data['condition'];
            $product->price = $data['price'];
            $product->weight = $data['weight'];
            $product->stock = $data['stock'];
            $product->save();

            $productImageRepository = new ProductImageRepository;

            if (isset($data['product_images'])) {
                foreach ($data['product_images'] as $productImage) {
                    $productImageRepository->create([
                        'product_id' => $product->id,
                        'image' => $productImage['image'],
                        'is_thumbnail' => $productImage['is_thumbnail'] ?? false,
                    ]);
                }
            }

            DB::commit();

            return $product->load('productImages');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(?array $data, ?string $id)
    {
        DB::beginTransaction();
        try {
            $product = Product::find($id);
            $product->store_id = $data['store_id'];
            $product->product_category_id = $data['product_category_id'];
            if (isset($data['name']) && $data['name'] !== $product->name) {
                $product->name = $data['name'];
                $product->slug = SlugHelper::createSlug(Product::class, $data['name'], 'slug');
            }
            $product->about = $data['about'];
            $product->condition = $data['condition'];
            $product->price = $data['price'];
            $product->weight = $data['weight'];
            $product->stock = $data['stock'];
            $product->save();

            $productImageRepository = new ProductImageRepository;

            // ====================
            // 1. DELETE
            // ====================
            // if (! empty($data['deleted_product_images'])) {
            //     foreach ($data['deleted_product_images'] as $id) {
            //         $image = $product->productImages()->find($id);
            //         if ($image) {
            //             Storage::disk('public')->delete($image->image);
            //             $productImageRepository->delete($id);
            //         }
            //     }
            // }

            // ====================
            // 2. CREATE / UPDATE
            // ====================
            // if (! empty($data['product_images'])) {
            //     foreach ($data['product_images'] as $item) {

            //         // 🔹 CREATE — tidak ada id
            //         if (empty($item['id'])) {
            //             if (isset($item['image']) && $item['image'] instanceof UploadedFile) {
            //                 $productImageRepository->create([
            //                     'product_id' => $product->id,
            //                     'image' => $item['image'], // ✅ kirim UploadedFile langsung
            //                     'is_thumbnail' => $item['is_thumbnail'] ?? false,
            //                 ]);
            //             }
            //         }

            // 🔹 UPDATE — ada id
            // 🔹 UPDATE — ada id
            //         else {
            //             $image = $product->productImages()->find($item['id']);

            //             if ($image) {
            //                 if (isset($item['image']) && $item['image'] instanceof UploadedFile) {
            //                     Storage::disk('public')->delete($image->image);
            //                     $image->image = $item['image'];
            //                 }

            //                 if (isset($item['is_thumbnail'])) {
            //                     // ✅ kalau set jadi true, reset semua dulu
            //                     if ($item['is_thumbnail'] == true) {
            //                         $product->productImages()->update(['is_thumbnail' => false]);
            //                     }
            //                     $image->is_thumbnail = $item['is_thumbnail'];
            //                 }

            //                 $image->save();
            //             }
            //         }
            //     }
            // }

            // ====================
            // 3. HANDLE THUMBNAIL
            // ====================
            // if (isset($data['thumbnail_id'])) {
            //     $exists = $product->productImages()
            //         ->where('id', $data['thumbnail_id'])
            //         ->exists();

            //     if ($exists) {
            //         $product->productImages()->update(['is_thumbnail' => false]);
            //         $product->productImages()
            //             ->where('id', $data['thumbnail_id'])
            //             ->update(['is_thumbnail' => true]);
            //     }
            // }

            if (isset($data['deleted_product_images'])) {
                foreach ($data['deleted_product_images'] as $productImage) {
                    $productImageRepository->delete($productImage);
                }
            }

            if (isset($data['product_images'])) {
                foreach ($data['product_images'] as $productImage) {
                    if (! isset($productImage['id'])) {
                        $productImageRepository->create([
                            'product_id' => $product->id,
                            'image' => $productImage['image'],
                            'is_thumbnail' => $productImage['is_thumbnail'],
                        ]);
                    }
                }
            }

            DB::commit();

            return $product->load('productImages');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(?string $id)
    {
        DB::beginTransaction();
        try {
            $product = Product::find($id);
            $product->delete();
            DB::commit();

            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
