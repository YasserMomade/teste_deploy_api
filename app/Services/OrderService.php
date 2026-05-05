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

    public function getAllOrders()
    {
        return Order::with(['client', 'category', 'responsible'])->get();
    }

    public function getOrderById(int $id): ?Order   
    {
        return Order::whith('client')->findOrFail($id);
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