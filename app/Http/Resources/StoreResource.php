<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        $frequentCategory = null;

        if ($this->relationLoaded('orders')) {

            $categoryCounts = $this->orders->groupBy('category_id')
                ->map(fn($orders) => $orders->count());

            $mostFrequentCategoryId = $categoryCounts->sortDesc()->keys()->first();

            $frequentCategory = optional($this->orders->firstWhere('category_id', $mostFrequentCategoryId)
            ?->category)->category;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'orders' => ['total_orders' => $this->orders_count ?? 0,],
            'frequent_category' => $frequentCategory ?? 'N/A',
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
