<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryService
{
    public function createCategory(array $data)
    {
        return Category::create($data);
    }

    public function getAllCategory(Request $request)
    {
        $query = Category::with('prices')
            ->withCount('orders')
            ->withSum('orders', 'weight')
            ->with(['orders.invoice']);

        if ($request->filled('search')) {
            $query->where('category', 'like', '%' . $request->search . '%');
        }

        return $query->get()->map(function ($category) {
            $totalAmount = $category->orders
                ->whereNotNull('invoice')
                ->sum(fn($order) => $order->invoice->amountTo_pay);

            return [
                'id'           => $category->id,
                'category'     => $category->category,
                'prices'       => $category->prices,        // ← array completo, não [0]
                'total_orders' => $category->orders_count,
                'total_kg'     => $category->orders_sum_weight ?? 0,
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