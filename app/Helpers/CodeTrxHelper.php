<?php

namespace App\Helpers;

use App\Models\Transaction;

class CodeTrxHelper
{
    public static function generateTransactionCode(): string
    {
        do {
            $last = Transaction::orderBy('id', 'desc')->first();
            $number = $last ? ((int) substr($last->code, 3)) + 1 : 1;

            $code = 'TRX'.str_pad($number, 5, '0', STR_PAD_LEFT);

        } while (Transaction::where('code', $code)->exists());

        return $code;
    }
}
