<?php

namespace App\Http\Requests;

use App\Models\ProductCategory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            'product_category_id' => [
                'required',
                'exists:product_categories,id',
                function ($attribute, $value, $fail) {
                    $category = ProductCategory::find($value);

                    if ($category && $category->parent_id === null) {
                        $fail('Kategori produk harus memiliki kategori induk');
                    }
                },
            ],
            'name' => 'required|string',
            'about' => 'required|string',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'weight' => 'required|decimal:0,2',
            'condition' => 'required|string|in:new,seccond',
            'product_images' => [
                'nullable',
                'array',
                'min:1',
                function ($attribute, $value, $fail) {
                    $trueCount = collect($value)
                        ->where('is_thumbnail', true)
                        ->count();

                    if ($trueCount > 1) {
                        $fail('Hanya boleh true 1 thumbnail.');
                    }

                    if ($trueCount === 0) {
                        $fail('Harus ada true 1 thumbnail.');
                    }
                },
            ],
            'product_images.*.image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'product_images.*.is_thumbnail' => 'required|boolean|',
        ];
    }

    public function attributes()
    {
        return parent::attributes();
    }
}
