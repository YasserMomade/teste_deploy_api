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

    public function statisc()
    {
        $totalOrders = Order::count();

        $totalWeight = Order::sum('weight');

        $totalRevenue = Order::join('invoices', 'invoices.id', '=', 'orders.invoice_id')
            ->sum('invoices.amountTo_pay');

        // $serviceTypes = Order::select(
        //     'service_type',
        //     DB::raw('count(*) as total')
        // )
        // ->groupBy('service_type')
        // ->get();

        return response()->json([
            'total_orders' => $totalOrders,
            'total_weight' => $totalWeight,
            'total_revenue' => $totalRevenue,
            // 'service_types' => $serviceTypes
        ]);
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