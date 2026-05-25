<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClient extends FormRequest
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
            'phone' => 'sometimes|string|max:20',
            'email' => 'sometimes|email',
            'id_costumer' => ['sometimes', 'integer'],
            'id_selectedOrder' => ['sometimes', 'array'],
            'id_selectedOrder.*' => ['integer'],
        ];
    }
}
