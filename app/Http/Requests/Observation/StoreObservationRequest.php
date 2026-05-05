<?php

namespace App\Http\Requests\Observation;

use App\Enums\ObservationLevelEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreObservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Qualquer utilizador autenticado
    }

    public function rules(): array
    {
        return [
            'description' => ['required', 'string'],
            'level'=> ['required', 'string', Rule::in(ObservationLevelEnum::values())],
        ];
    }
}