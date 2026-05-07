<?php

namespace App\Services\Reports;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class FinancialReportService
{
    public function generate(array $filters = []): array
    {
        $query = $this->buildBaseQuery($filters);

        $orders = (clone $query)
            ->select('orders.*')
            ->with([
                'client:id,name,lastname',
                'invoice',
                'responsible:id,name,user_code',
            ])
            ->get();
        return [
            'summary'            => $this->buildSummary(clone $query),
            'by_payment_status'  => $this->groupByPaymentStatus(clone $query),
            'by_payment_method'  => $this->groupByPaymentMethod(clone $query),
            'daily_totals'       => $this->getDailyTotals(clone $query),
            'orders'             => $orders,
            'filters_applied'    => $filters,
        ];
    }

    private function buildBaseQuery(array $filters)
    {
        return Order::query()
            ->join('invoices', 'orders.invoice_id', '=', 'invoices.id')
            ->when(
                isset($filters['date_from']),
                fn($q) => $q->whereDate('orders.reception_date', '>=', $filters['date_from'])
            )
            ->when(
                isset($filters['date_to']),
                fn($q) => $q->whereDate('orders.reception_date', '<=', $filters['date_to'])
            )
            ->when(
                isset($filters['destination']),
                fn($q) => $q->where('orders.destination', 'like', "%{$filters['destination']}%")
            )
            ->when(
                isset($filters['payment_status']),
                fn($q) => $q->where('invoices.payment_status', $filters['payment_status'])
            )
            ->when(
                isset($filters['payment_method']),
                fn($q) => $q->where('invoices.payment_method', $filters['payment_method'])
            )
            ->when(
                isset($filters['store_id']),
                fn($q) => $q->where('orders.store_id', $filters['store_id'])
            );
    }
    private function buildSummary($query): array
    {
        $data = (clone $query)
            ->selectRaw('

            
                COUNT(*) as total_orders,
                SUM(invoices.amountTo_pay) as total_to_pay,
                SUM(invoices.amount_paid) as total_paid,
                SUM(invoices.amountTo_pay - COALESCE(invoices.amount_paid, 0)) as total_debt,
                COUNT(CASE WHEN invoices.payment_status = "paid" THEN 1 END) as total_paid_orders,
                COUNT(CASE WHEN invoices.payment_status = "pendent" THEN 1 END) as total_pendent_orders,
                COUNT(CASE WHEN invoices.payment_status = "faild" THEN 1 END) as total_failed_orders
            ')
            ->first();

        return [
            'total_orders'         => (int) $data->total_orders,
            'total_to_pay'         => round((float) $data->total_to_pay, 2),
            'total_paid'           => round((float) $data->total_paid, 2),
            'total_debt'           => round((float) $data->total_debt, 2),
            'total_paid_orders'    => (int) $data->total_paid_orders,
            'total_pendent_orders' => (int) $data->total_pendent_orders,
            'total_failed_orders'  => (int) $data->total_failed_orders,
        ];
    }

    private function groupByPaymentStatus($query): array
    {
        return (clone $query)
            ->selectRaw('
                invoices.payment_status as status,
                COUNT(*) as total_orders,
                SUM(invoices.amountTo_pay) as total_amount,
                SUM(invoices.amount_paid) as total_paid
            ')
            ->groupBy('invoices.payment_status')
            ->get()
            ->map(fn($row) => [
                'status'        => $row->status,
                'total_orders'  => (int) $row->total_orders,
                'total_amount'  => round((float) $row->total_amount, 2),
                'total_paid'    => round((float) $row->total_paid, 2),
            ])
            ->toArray();
    }

    private function groupByPaymentMethod($query): array
    {
        return (clone $query)
            ->selectRaw('
                invoices.payment_method as method,
                COUNT(*) as total_orders,
                SUM(invoices.amount_paid) as total_collected
            ')
            ->groupBy('invoices.payment_method')
            ->get()
            ->map(fn($row) => [
                'method'          => $row->method,
                'total_orders'    => (int) $row->total_orders,
                'total_collected' => round((float) $row->total_collected, 2),
            ])
            ->toArray();
    }

    private function getDailyTotals($query): array
    {
        return (clone $query)
            ->selectRaw('
                DATE(orders.reception_date) as date,
                COUNT(*) as total_orders,
                SUM(invoices.amountTo_pay) as total_to_pay,
                SUM(invoices.amount_paid) as total_paid,
                SUM(orders.weight) as total_weight
            ')
            ->groupBy(DB::raw('DATE(orders.reception_date)'))
            ->orderBy('date')
            ->get()
            ->map(fn($row) => [
                'date'          => $row->date,
                'total_orders'  => (int) $row->total_orders,
                'total_to_pay'  => round((float) $row->total_to_pay, 2),
                'total_paid'    => round((float) $row->total_paid, 2),
                'total_weight'  => round((float) $row->total_weight, 3),
            ])
            ->toArray();
    }
}
