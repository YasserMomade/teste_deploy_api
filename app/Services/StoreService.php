<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;


class StoreService
{

    public function list(array $filters = []): LengthAwarePaginator
    {
        return Store::query()
        ->withCount('orders')
        ->with(['orders.category'])
        ->when(
            isset($filters['search']),
            fn($q) => 
            $q->where('name', 'like', "%{$filters['search']}%")
        )->orderBy('name')->paginate($filters['per_page'] ?? 15);
    }

 

    public function create(array $data): Store 
    {
        return Store::create($data);
    }

    
    public function update(Store $store, array $data): Store
    {
        $store->update($data);
        return $store->fresh();
    }

    public function delete(Store $store): void
    {

        $store->delete();
    }




}