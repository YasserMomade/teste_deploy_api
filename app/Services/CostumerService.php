<?php

namespace App\Services;

use App\Models\Costumer;

class CostumerService
{
    public function createCostumer(array $data): Costumer
    {
        return Costumer::create($data);
    }

    public function getAllCostumers($request = null, int $perPage = 15)
    {
        $query = Costumer::with([
            'orderRequest',
            'fileCostumer'
        ])->latest();

        if ($request) {

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT(name, ' ', lastname) LIKE ?", ["%{$search}%"]);
                });
            }
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }
        }

        return $query->paginate($perPage);
    }

    public function getCostumerById(int $id): ?Costumer
    {
        return Costumer::with([
            'orderRequest',
            'fileCostumer'
        ])
            ->findOrFail($id);
    }

    public function updateCostumer(Costumer $Costumer, array $data): Costumer
    {
        $Costumer->update($data);
        return $Costumer;
    }

    public function deleteCostumer(string $id)
    {
        $Costumer = Costumer::findOrFail($id);
        return $Costumer->delete();
    }
}
