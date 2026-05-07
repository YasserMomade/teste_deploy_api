<?php

namespace App\Services;

use App\Models\Costumer;

class CostumerService
{
    public function createCostumer(array $data): Costumer
    {
        return Costumer::create($data);
    }

    public function getAllCostumers()
    {
        return Costumer::with('orderRequest')->get();
    }

    public function getCostumerById(int $id): ?Costumer
    {
        return Costumer::findOrFail($id);
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