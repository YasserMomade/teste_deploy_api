<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ObservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'level' => $this->level->value,
            'level_label' => $this->level->label(),
            'order_id' => $this->order_id,
            'created_by' => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
                'user_code' => $this->creator->user_code,
            ],
            'can_edit' => $request->user()?->id === $this->created_by,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}