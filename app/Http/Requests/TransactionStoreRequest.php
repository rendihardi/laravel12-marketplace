<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TransactionStoreRequest extends FormRequest
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
            'store_id' => 'required|exists:stores,id',
            'buyer_id' => 'required|exists:buyers,id',
            'address_id' => 'required|integer',
            'address' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'shipping' => 'required|string',
            'shipping_type' => 'required|string',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.qty' => 'required|integer',
        ];
    }

    public function attributes()
    {
        return parent::attributes();
    }
}
