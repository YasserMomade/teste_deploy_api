<?php

namespace App\Http\Requests\Order;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'service_type' => 'sometimes|string',
            'weight' => 'sometimes|numeric',
            'status' => [
                'sometimes',
                Rule::in([
                    'recebido_lisboa',
                    'em_processamento',
                    'pronto_expedicao',
                    'expedido',
                    'em_transito',
                    'recebido_mocambique',
                    'pronto_levantamento',
                    'entregue',
                ]),
            ],
            'declared_weight' => 'sometimes|numeric',
            'category_id' => 'sometimes|exists:categories,id',
            'responsible_id' => 'nullable|exists:users,id',
            'store_id' => 'nullable|exists:stores,id',
        ];
    }
}
