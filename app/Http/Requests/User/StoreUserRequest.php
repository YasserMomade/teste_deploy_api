<?php

namespace App\Http\Requests\User;

use App\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255'],
            'lastname'   => ['required', 'string', 'max:255'],
            'phone'      => ['required', 'string', 'max:50'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'role'       => ['required', 'string', Rule::in(RoleEnum::values())],
            'counter_id' => [
                Rule::requiredIf(fn() => !in_array($this->input('role'), [
                    RoleEnum::Admin->value,
                    RoleEnum::Expedidor->value,
                    RoleEnum::Contabilista->value,
                ])),
                'nullable',
                'integer',
                'exists:counters,id',
            ],
        ];
    }

    public function messages(): array
{
    return [
        'counter_id.required' => 'O balcão é obrigatório para este tipo de utilizador.',
    ];
}
}
