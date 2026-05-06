<?php

namespace App\Http\Requests\Status;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStatus extends FormRequest
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
            'descryption' => 'required|in:recebido_lisboa,em_processamento,pronto_expedicao,expedido,em_transito,recebido_mocambique,pronto_levantamento,entregue',
            'responsible_id' => 'required|exists:users,id',
            'order_id' => 'required|string|exists:orders,id'
        ];

    }
}
