<?php

namespace App\Http\Requests\Shipment;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipment extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reference' => 'required|string|max:255|unique:shipments,reference',
            'carta_porte' => 'required|string|max:255',
            'airline' => 'required|in:TAAG,TAP',
            'origin' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'shipment_date' => 'required|date',
            'general_observations' => 'nullable|string',
            'order_ids' => 'required|array|min:1',
            'order_ids.*' => 'required|exists:orders,id',
        ];
    }

    public function messages(): array
    {
        return [
            'reference.required' => 'O código de referência é obrigatório.',
            'reference.unique' => 'Já existe um envio com este código de referência.',
            'carta_porte.required' => 'O número de carta de porte é obrigatório.',
            'airline.required' => 'A companhia aérea é obrigatória.',
            'airline.in' => 'A companhia aérea deve ser TAAG ou TAP.',
            'shipment_date.required' => 'A data do envio é obrigatória.',
            'order_ids.required' => 'Selecione pelo menos uma encomenda.',
            'order_ids.min' => 'Selecione pelo menos uma encomenda.',
        ];
    }
}
