<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreBalanceHistory extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'store_balance_id',
        'type',
        'reference_id',
        'reference_type',
        'amount',
        'remark',
    ];

    public function storeBalance()
    {
        return $this->belongsTo(StoreBalance::class);
    }
}
