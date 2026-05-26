<?php

namespace App\Services\Reports;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OperationalReportService
{
    public function generate(array $filters = []): array
    {
        $query = $this->buildBaseQuery($filters);

        $orders = (clone $query)
            ->with([
                'client:id,name,lastname',
                'category:id,category',
                'store:id,name',
                'latestStatus:status.id,status.order_id,status.descryption,status.created_at',
                'responsible:id,name,lastname,user_code',
            ])
            ->get();

        return [
            'summary' => $this->buildSummary($query),
            'by_destination' => $this->groupByDestination(clone $query),
            'by_status' => $this->groupByStatus(clone $query),
            'by_service_type' => $this->groupByServiceType(clone $query),
            'by_category' => $this->groupByCategory(clone $query),
            'by_store' => $this->groupByStore(clone $query),
            'orders' => $orders,
            'filters_applied' => $filters,
        ];
    }

    private function buildBaseQuery(array $filters)
    {
        return Order::query()
            ->when(
                isset($filters['date_from']),
                fn($q) => $q->whereDate('created_at', '>=', $filters['date_from'])
            )
            ->when(
                isset($filters['date_to']),
                fn($q) => $q->whereDate('created_at', '<=', $filters['date_to'])
            )
            ->when(
                isset($filters['destination']),
                fn($q) => $q->where('destination', 'like', "%{$filters['destination']}%")
            )
            ->when(
                isset($filters['origin']),
                fn($q) => $q->where('origin', 'like', "%{$filters['origin']}%")
            )
            ->when(
                isset($filters['store_id']),
                fn($q) => $q->where('store_id', $filters['store_id'])
            )
            ->when(
                isset($filters['category_id']),
                fn($q) => $q->where('category_id', $filters['category_id'])
            );
    }

    private function buildSummary($query): array
    {
        $data = (clone $query)->selectRaw('
            COUNT(*) as total_orders,
            SUM(weight) as total_weight,
            SUM(declared_weight) as total_declared_weight,
            SUM(volume_number) as total_volumes
        ')->first();

        return [
            'total_orders' => (int) $data->total_orders,
            'total_weight' => round((float) $data->total_weight, 3),
            'total_declared_weight' => round((float) $data->total_declared_weight, 3),
            'total_volumes' => (int) $data->total_volumes,
        ];
    }

    private function groupByDestination($query): array
    {
        return (clone $query)
            ->selectRaw('destination, COUNT(*) as total, SUM(weight) as total_weight')
            ->groupBy('destination')
            ->orderByDesc('total')
            ->get()
            ->map(fn($row) => [
                'destination'  => $row->destination,
                'total_orders' => (int) $row->total,
                'total_weight' => round((float) $row->total_weight, 3),
            ])
            ->toArray();
    }

    private function groupByStatus($query): array
    {
        return (clone $query)
            ->leftJoin(DB::raw('(
                SELECT order_id, descryption
                FROM status s1
                WHERE created_at = (
                    SELECT MAX(created_at)
                    FROM status s2
                    WHERE s2.order_id = s1.order_id
                    AND s2.deleted_at IS NULL
                )
                AND s1.deleted_at IS NULL
            ) as latest_status'), 'orders.id', '=', 'latest_status.order_id')
            ->selectRaw('COALESCE(latest_status.descryption, "sem_estado") as status, COUNT(*) as total')
            ->groupBy('latest_status.descryption')
            ->orderByDesc('total')
            ->get()
            ->map(fn($row) => [
                'status' => $row->status,
                'total'  => (int) $row->total,
            ])
            ->toArray();
    }

    private function groupByServiceType($query): array
    {
        return (clone $query)
            ->selectRaw('service_type, COUNT(*) as total')
            ->groupBy('service_type')
            ->orderByDesc('total')
            ->get()
            ->map(fn($row) => [
                'service_type' => $row->service_type,
                'total'        => (int) $row->total,
            ])
            ->toArray();
    }

    private function groupByCategory($query): array
    {
        return (clone $query)
            ->join('categories', 'orders.category_id', '=', 'categories.id')
            ->selectRaw('categories.category as category, COUNT(*) as total')
            ->groupBy('categories.category')
            ->orderByDesc('total')
            ->get()
            ->map(fn($row) => [
                'category' => $row->category,
                'total'    => (int) $row->total,
            ])
            ->toArray();
    }

    private function groupByStore($query): array
    {
        return (clone $query)
            ->join('stores', 'orders.store_id', '=', 'stores.id')
            ->selectRaw('stores.name as store, COUNT(*) as total')
            ->groupBy('stores.name')
            ->orderByDesc('total')
            ->get()
            ->map(fn($row) => [
                'store' => $row->store,
                'total' => (int) $row->total,
            ])
            ->toArray();
    }
}