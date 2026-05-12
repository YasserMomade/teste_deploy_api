<?php

namespace App\Services;

use App\Models\Orders_request;

class OrderRequestService 
{
     public function createOrder(array $data): Orders_request
    {
        return Orders_request::create($data);
    }

    public function getAllOrders()
    {
        return Orders_request::all();
    }

    public function getOrderById(int $id): ?Orders_request   
    {
        return Orders_request::findOrFail($id);
    }

    public function updateOrder(string $id, array $data): Orders_request
    {
        $order = Orders_request::findOrFail($id);
        $order->update($data);
        return $order;
    }

    public function deleteOrder(Order $order): void
    {
        $order->delete();
    }
}