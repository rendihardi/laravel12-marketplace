<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'store_id',
        'product_category_id',
        'name',
        'slug',
        'about',
        'condition',
        'price',
        'weight',
        'stock',
    ];

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%'.$search.'%')
            ->whereHas('productCategory', function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%');
            });
    }

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function productReviews()
    {
        return $this->hasMany(ProductReview::class);
    }
}
