<?php

namespace App\Http\Requests\Order;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrder extends FormRequest
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
           'client_id' => 'nullable|exists:clients,id',
            'description' => 'required|string',
            'tracking' => 'required|string|unique:orders,tracking',
            'origin' => 'required|string',
            'destination' => 'required|string',
            'reception_date' => 'required|date',
            'service_type' => 'required|string',
            'volume_number' => 'required|integer',
            'weight' => 'required|numeric',
            'declared_weight' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'responsible_id' => 'nullable|exists:users,id',
            'store_id' => 'nullable|exists:stores,id',
        ];
    }
}
