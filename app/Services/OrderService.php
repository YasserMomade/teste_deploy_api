<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderService
{
    public function createOrder(array $data): Order
    {
        return Order::create($data);
    }

    public function getAllOrders()
    {
        return Order::with([
            'client',
            'category',
            'responsible',
            'category.prices',
            'invoice',
            'status',
            'store'
        ])->get();
    }


    public function getOrderById(int $id): ?Order   
    {
        return Order::with([
            'client',
            'category',
            'responsible',
            'category.prices',
            'invoice'
        ])->findOrFail($id);
    }

    public function updateOrder(Order $order, array $data): Order
    {
        $order->update($data);
        return $order;
    }

    public function deleteOrder(Order $order): void
    {
        $order->delete();
    }

   

}