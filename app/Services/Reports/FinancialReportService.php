<?php

namespace App\Services\Reports;

use App\Models\Order;

class FinancialReportService
{
    public function generate(array $filters = []): array
    {
        $query = $this->buildBaseQuery($filters);

        $orders = (clone $query)->whit([
            'client:id,name,lastname',
            'invoice',
            'responsible:id,name,user_code'
        ])->get();

        return [
            'summary' => $this->buildSummary(clone $query),
            'by_payment_status' => $this->groupByPaymentStatus(clone $query),
            'by_payment_method' => $this->groupByPaymentMethod(clone $query),
            'daily_totals' => $this->getDailyTotals(clone $query),

            'orders' => $orders,
            'filter_applied' => $filters,
        ];
    }

    public function buildBaseQuery(array $filters)
    {
        return Order::query()->join('invoices', 'orders.invoice_id', '=', 'invoices.id')
            ->when(
                isset($filters['date_from']),
                fn($q) => $q->whereDate('orders.reception_date', '>=', $filters['date_from'])
            )->when(
                isset($filters['date_to']),
                fn($q) => $q->whereDate('orders.reception_date', '<=', $filters['date_to'])
            )->when(
                isset($filters['destination']),
                fn($q) => $q->where('orders.destination', 'like', $filters['destination'])
            )->when(
                isset($filters['patment_status']),
                fn($q) => $q->where('invoices.payment_status', 'like', $filters['payment_status'])
            )->when(
                isset($filters['payment_method']),
                fn($q) => $q->where('invoices.payment_method', 'like', $filters['payment_method'])
            )->when(
                isset($filters['store_id']),
                fn($q) => $q->where('orders.store_id', 'like', $filters['store_id'])
            )->select('orders.*');
    }

    private function buildSummary($query): array
    {
        $data = (clone $query)->selectRaw('
        Count(*) as total_orders, 
        SUM(invoices.amountTo_pay) as total_to_pay,
        SUM(invoices.amount_paid) as total_paid,
        Sum(invoices.amountTo_pay - COALESCE(invoices.amount_paid, 0)) as total_debt,
        COUNT(CASE WHEN invoices.payment_status = "paid" THEN 1 END) as total_paid_orders,
        COUNT(CASE WHEN invoices.payment_status = "pendent" THEN 1 END) as total_pendent_orders,
        COUNT(CASE WHEN invoices.payment_status = "faild" THEN 1 END) as total_failed_orders,
    ')->first();

        return [
            'total_orders' => (int) $data->total_orders,
            'total_to_pay' => round((float) $data->total_to_pay, 2),
            'total_paid' => round((float) $data->total_paid, 2),
            'total_debt' => round((float) $data->total_debt, 2),
            'total_paid_orders' => (int) $data->total_paid_orders,
            'total_pendent_orders' => (int) $data->total_pendent_orders,
            'total_failed_orders' => (int) $data->total_failed_orders,

        ];
    }

    public function groupByPaymentMethod($query): array
    {
        //TODO:: implement groupByPaymentMethod
    }

    public function groupByPaymentStatus($query): array
    {
        //TODO:: implement groupByPaymentStatus
    }

    public function getDailyTotals($query): array
    {
        //TODO:: implement getDailyTotals
    }
}
