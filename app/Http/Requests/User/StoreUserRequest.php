<?php

namespace App\Http\Requests\User;

use App\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

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
            'email'      => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'role'       => ['required', 'string', Rule::in(RoleEnum::values())],
            'counter_id' => [
                Rule::requiredIf(fn() => $this->input('role') !== RoleEnum::Admin->value),
                'nullable',
                'integer',
                'exists:counters,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'counter_id.required' => 'A counter is required for non-admin users.',
        ];
    }
}