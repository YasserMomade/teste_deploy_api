<?php

namespace App\Http\Requests\Country;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCountryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         return [
            'name' => ['sometimes', 'string', 'max:100', Rule::unique('countries', 'name')->ignore($this->route('country'))],
            'iva' => ['sometimes', 'numeric', 'min: 0'],
            'coin' => ['sometimes', 'string', 'max: 55'],
        ];
    }
}