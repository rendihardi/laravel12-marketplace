<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStoreRequest extends FormRequest
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
            'name' => 'required|string',
            'user_id' => 'required|uuid|exists:users,id',
            'logo' => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'about' => 'required|string',
            'phone' => 'required|string',
            'address_id' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'is_verified' => 'required|boolean',
        ];
    }

    public function attributes()
    {
        return parent::attributes();
    }

    // public function prepareForValidation()
    // {
    //     $this->merge([
    //         'user_id' => $this->user()->id,
    //     ]);
    // }
}
