<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuyerUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            'profile_picture' => 'nullable|image|mimes:png,jpg,jpeg',
            'phone_number' => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return parent::attributes();
    }
}
