<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductCategoryStoreRequest extends FormRequest
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
            'name' => 'nullable|string|unique:product_categories,name',
            'tagline' => 'required|string',
            'description' => 'required|string',
            'parent_id' => 'nullable|uuid|exists:product_categories,id',
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ];
    }

    public function attributes()
    {
        return parent::attributes();
    }
}
