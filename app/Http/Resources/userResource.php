<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'user_code'  => $this->user_code,
            'role'       => $this->role->value,
            'name'       => $this->name,
            'lastname'   => $this->lastname,
            'phone'      => $this->phone,
            'email'      => $this->email,
            'counter'    => new CounterResource($this->whenLoaded('counter')),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}