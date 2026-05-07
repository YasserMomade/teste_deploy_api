<?php

namespace App\Http\Requests\OrderRequest;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'description' => 'required|string',
            'quantity' => 'required|numeric',
            'costumer_id' => 'required|exists:costumers,id',
            'store_id' => 'required|exists:stores,id',
        ];
    }
}
