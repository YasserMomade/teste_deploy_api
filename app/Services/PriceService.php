<?php

namespace App\Services;

use App\Models\Price;

class PriceService 
{
    public function createPrice(array $data)
    {
        return Price::create($data);
    }

    public function getAllPrice()
    {
        return Price::with('category')->get();
    }

    public function getPriceByCategoryId(string $id) 
    {
        return Price::where('category_id', $id)->firstOrFail()->id;
    }
    public function getPriceById(string $id)
    {
        return Price::with('category')->findOrFail($id);
    }

    public function updatePrice(string $id, array $data)
    {
        $price = Price::findOrFail($id);
        $price->update($data);

        return $price;
    }

    public function deletePrice(string $id)
    {
        $price = Price::findOrFail($id);
        return $price->delete();
    }
}