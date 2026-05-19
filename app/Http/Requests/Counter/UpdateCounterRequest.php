<?php

namespace App\Http\Requests\Counter;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCounterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
       // return $this->user()->isAdmin();
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
            'name' => ['sometimes', 'string', 'max: 100'],
            'country_id' => ['sometimes', 'integer', 'exists:countries,id'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }
}