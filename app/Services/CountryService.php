<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;


class CountryService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        return Country::query()
            ->when(
                isset($filters['search']),
                fn($q) =>
                $q->where('name', 'like', "%{$filters['search']}%")
            )->orderBy('name')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): Country
    {
        return Country::create($data);
    }

    public function update(Country $country, array $data): Country
    {
        $country->update($data);
        return $country->fresh();
    }

    public function delete(Country $country): void
    {

        if ($country->counters()->exists()) {
            throw new ConflictHttpException(
                'Cannot delete a country that has counters assigned.'
            );
        }

        $country->delete();
    }
}
