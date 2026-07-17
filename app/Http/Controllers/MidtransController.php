<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        try {
            \Midtrans\Config::$serverKey = config('midtrans.serverKey');
            \Midtrans\Config::$isProduction = config('midtrans.isProduction');
            \Midtrans\Config::$isSanitized = config('midtrans.isSanitized');
            \Midtrans\Config::$is3ds = config('midtrans.is3ds');

            $notification = new \Midtrans\Notification();

            $transactionStatus = $notification->transaction_status;
            $type = $notification->payment_type;
            $orderId = $notification->order_id;
            $fraudStatus = $notification->fraud_status;

            $transaction = Transaction::where('code', $orderId)->first();

            if (!$transaction) {
                Log::warning('Midtrans Callback: Transaction code ' . $orderId . ' not found.');
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            $paymentStatus = 'unpaid';
            $status = 'unpaid';

            if ($transactionStatus == 'capture') {
                if ($type == 'credit_card') {
                    if ($fraudStatus == 'challenge') {
                        $paymentStatus = 'unpaid';
                        $status = 'unpaid';
                    } else {
                        $paymentStatus = 'paid';
                        $status = 'pending';
                    }
                }
            } elseif ($transactionStatus == 'settlement') {
                $paymentStatus = 'paid';
                $status = 'pending';
            } elseif ($transactionStatus == 'pending') {
                $paymentStatus = 'unpaid';
                $status = 'unpaid';
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $paymentStatus = 'unpaid';
                $status = 'cancelled';
            }

            $transaction->payment_status = $paymentStatus;
            $transaction->status = $status;
            $transaction->save();

            Log::info('Midtrans Callback Success: Transaction code ' . $orderId . ' updated to status ' . $status . ' and payment_status ' . $paymentStatus);

            return response()->json(['message' => 'Callback processed successfully']);
        } catch (\Throwable $e) {
            Log::error('Midtrans Callback Error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
