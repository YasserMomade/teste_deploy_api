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

    public function getAllOrders($request, int $getPaginate = 15)
    {
        $query = Order::with([
            'client',
            'category',
            'responsible',
            'category.prices',
            'invoice',
            'latestStatus.responsible',
            'store',
            'file'
        ]);

        // Pesquisa
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('tracking', 'like', "%{$search}%")

                    ->orWhereHas('client', function ($client) use ($search) {

                        $client->where('name', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('destination')) {

            $query->where('destination', $request->destination);
        }
        if ($request->filled('service_type')) {

            $query->where('service_type', $request->service_type);
        }

        if ($request->filled('payment_status')) {

            $query->whereHas('invoice', function ($invoice) use ($request) {

                $invoice->where(
                    'payment_status',
                    $request->payment_status
                );
            });
        }
        if ($request->filled('order_status')) {

            $query->whereHas('latestStatus', function ($status) use ($request) {

                $status->where(
                    'descryption',
                    $request->order_status
                );
            });
        }
        if ($request->filled('start_date')) {

            $query->whereDate(
                'reception_date',
                '>=',
                $request->start_date
            );
        }
        if ($request->filled('end_date')) {

            $query->whereDate(
                'reception_date',
                '<=',
                $request->end_date
            );
        }
        return $query->latest()->paginate($getPaginate);
    }



    public function getOrderById(int $id): ?Order
    {
        return Order::with([
            'client',
            'category',
            'responsible',
            'category.prices',
            'invoice',
            'status.responsible',
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
            'status.responsible',
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
