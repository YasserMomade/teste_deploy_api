<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function createCategory(array $data)
    {
        return Category::create($data);
    }

    public function getAllCategory()
    {
        return Category::with('prices')
            ->withCount('orders')
            ->withSum('orders', 'weight')
            ->with(['orders.invoice'])  
            ->get()
            ->map(function ($category) {
                $totalAmount = $category->orders
                    ->whereNotNull('invoice')
                    ->sum(fn($order) => $order->invoice->amountTo_pay);

                return [
                    'id' => $category->id,
                    'category' => $category->category,
                    'prices' => $category->prices,
                    'total_orders' => $category->orders_count,
                    'total_kg' => $category->orders_sum_weight ?? 0,
                    'total_amount' => $totalAmount,
                ];
            });
    }

    public function getCategoryById(string $id)
    {
        return Category::findOrFail($id);
    }

    public function updateCategory(string $id, array $data)
    {
        $category = Category::findOrFail($id);
        $category->update($data);

        return $category;
    }

    public function deleteCategory(string $id)
    {
        $category = Category::findOrFail($id);
        return $category->delete();
    }
}