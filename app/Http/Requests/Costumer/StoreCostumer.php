<?php

namespace App\Http\Requests\Costumer;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCostumer extends FormRequest
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
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|min:2|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',

            'orders_request' => 'required|array|min:1',
                'orders_request.*.description' => 'required|string',
                'orders_request.*.quantity' => 'required|integer|min:1',
                'orders_request.*.store_name' => 'required|string',
        ];
    }
}
