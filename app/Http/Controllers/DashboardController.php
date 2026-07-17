<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\Product;
use App\Models\Store;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $role = $user->roles->first()?->name;

            $data = [];

            if ($role === 'admin') {
                $data = [
                    'total_revenue' => (float) Transaction::where('payment_status', 'paid')->sum('grand_total'),
                    'total_stores' => Store::count(),
                    'total_buyers' => Buyer::count(),
                    'total_products' => Product::count(),
                    'total_transactions' => Transaction::count(),
                ];
            } elseif ($role === 'store') {
                $store = $user->store;
                $storeId = $store?->id;

                $data = [
                    'total_revenue' => $storeId ? (float) Transaction::where('store_id', $storeId)->where('payment_status', 'paid')->sum('grand_total') : 0,
                    'total_products' => $storeId ? Product::where('store_id', $storeId)->count() : 0,
                    'total_buyers' => $storeId ? Transaction::where('store_id', $storeId)->distinct('buyer_id')->count('buyer_id') : 0,
                    'total_transactions' => $storeId ? Transaction::where('store_id', $storeId)->count() : 0,
                ];
            } elseif ($role === 'buyer') {
                $buyer = $user->buyer;
                $buyerId = $buyer?->id;

                $data = [
                    'total_expenses' => $buyerId ? (float) Transaction::where('buyer_id', $buyerId)->where('payment_status', 'paid')->sum('grand_total') : 0,
                    'total_products' => Product::count(),
                    'total_transactions' => $buyerId ? Transaction::where('buyer_id', $buyerId)->count() : 0,
                ];
            }

            return response()->json($data);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
