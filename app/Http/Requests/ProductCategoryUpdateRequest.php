<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductCategoryUpdateRequest extends FormRequest
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
            'name' => 'nullable|string',
            'tagline' => 'nullable|string',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|uuid|exists:product_categories,id',
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ];
    }

    public function attributes()
    {
        return parent::attributes();
    }
}
