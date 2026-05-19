<?php

namespace App\Http\Requests\Order;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'description' => 'sometimes|string',
            'tracking' => 'sometimes|string|unique:orders,tracking',
            'origin' => 'sometimes|string',
            'destination' => 'sometimes|string',
            'reception_date' => 'sometimes|date',
            'service_type' => 'sometimes|string',
            'volume_number' => 'sometimes|integer',
            'weight' => 'sometimes|numeric',
            'declared_weight' => 'sometimes|numeric',
            'category_id' => 'sometimes|exists:categories,id',
            'responsible_id' => 'nullable|exists:users,id',
            'store_id' => 'nullable|exists:stores,id',
    
        ];
    }
}
