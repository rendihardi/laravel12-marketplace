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
            'status' => 'required|string|in:processing,delivering,canceled,cancelled,completed',
            'delivery_proof' => 'nullable|image|mimes:png,jpg,jpeg|max:5048',
        ];
    }

    public function attributes(): array
    {
        return parent::attributes();
    }

    protected function prepareForValidation()
    {
        if ($this->has('delivery_status')) {
            $this->merge([
                'status' => $this->delivery_status,
            ]);
        }
    }
}
