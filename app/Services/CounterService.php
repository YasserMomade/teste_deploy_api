<?php

namespace App\Services;

use App\Models\Counter;
use Illuminate\Pagination\LengthAwarePaginator;

class CounterService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        return Counter::query()
            ->with('country')
            ->when(isset($filters['search']), fn($q) =>
                $q->where('name', 'like', "%{$filters['search']}%")
            )
            ->when(isset($filters['country_id']), fn($q) =>
                $q->where('country_id', $filters['country_id'])
            )
            ->orderBy('name')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): Counter
    {
        return Counter::create($data);
    }

    public function update(Counter $counter, array $data): Counter
    {
        $counter->update($data);
        return $counter->fresh(['country']);
    }

    public function delete(Counter $counter): void
    {
        if ($counter->users()->exists()) {
            throw new \Exception('Cannot delete a counter that has users assigned.');
        }

        $counter->delete();
    }
}