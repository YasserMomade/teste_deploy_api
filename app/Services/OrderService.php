<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Order;

class OrderService
{
    public function createOrder(array $data): Order
    {
        return Order::create($data);
    }

    public function getAllOrders(int $getPaginate = 15)
    {
        return Order::with([
            'client',
            'category',
            'responsible',
            'category.prices',
            'invoice',
            'status',
            'store',
            'file'
        ])->paginate($getPaginate);
    }

    public function getOrderById(int $id): ?Order   
    {
        return Order::with([
            'client',
            'category',
            'responsible',
            'category.prices',
            'invoice',
            'status',
            'store',
            'file'
        ])->findOrFail($id);
    }

    public function getOrderByTracking(string $tracking): ?Order
    {
        return Order::with([
            'client',
            'category',
            'responsible',
            'category.prices',
            'invoice',
            'status',
            'store',
            'file'
        ])
        ->where('tracking', $tracking)
        ->firstOrFail();
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