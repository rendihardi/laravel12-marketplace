<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class WithdrawalStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'store_balance_id' => 'required|uuid|exists:store_balances,id',
            'amount' => 'required|integer',
            'bank_name' => 'required|string|in:bca,bni,bri,mandiri',
            'bank_account_name' => 'required|string',
            'bank_account_number' => 'required|string',
        ];
    }

    public function attributes()
    {
        return parent::attributes();
    }
}
