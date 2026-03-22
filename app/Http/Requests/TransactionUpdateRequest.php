<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TransactionUpdateRequest extends FormRequest
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
            'tracking_number' => 'nullable|string',
            'status' => 'required|string|in:processing,delivering,canceled,completed',
            'delivery_proof' => 'nullable|image|mimes:png,jpg,jpeg|max:5048',
        ];
    }

    public function atributes(): array
    {
        return parent::attributes();
    }

    // public function prepareForValidation()
    // {
    //     $this->merge([
    //         'delivery_proof' => $this->delivery_proof ?? null,
    //         'tracking_number' => $this->tracking_number ?? null,
    //     ]);
    // }
}
