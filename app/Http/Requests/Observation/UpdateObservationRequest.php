<?php

namespace App\Http\Requests\Observation;

use App\Enums\ObservationLevelEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateObservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        //so quem criou pode editar
        return $this->route('observation')->isOwnedBy($this->user());
    }

    public function rules(): array
    {
        return [
            'description' => ['sometimes', 'string'],
            'level' => ['sometimes', 'string', Rule::in(ObservationLevelEnum::values())],
        ];
    }

    public function failedAuthorization(): never
    {
        throw new \Illuminate\Auth\Access\AuthorizationException(
            'You can only edit your own observations.'
        );
    }
}