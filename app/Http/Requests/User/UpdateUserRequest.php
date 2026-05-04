<?php

namespace App\Http\Requests\User;

use App\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name'       => ['sometimes', 'string', 'max:255'],
            'lastname'   => ['sometimes', 'string', 'max:255'],
            'phone'      => ['sometimes', 'string', 'max:50'],
            'email'      => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user_id)],
            'password'   => ['sometimes', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'role'       => ['sometimes', 'string', Rule::in(RoleEnum::values())],
            'counter_id' => ['sometimes', 'nullable', 'integer', 'exists:counters,id'],
        ];
    }
}