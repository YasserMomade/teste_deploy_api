<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;
use Override;

class FinancialReportExport implements
    FromCollection,
    WithHeadings,
    WithTitle,
    WithStyles,
    ShouldAutoSize
{

    public function __construct(private readonly array $data) {}

    public function collection(): Collection
    {
        return collect($this->data['orders'])->map(fn($order) => [
            $order->tracking,
            $order->client->name ?? 'N/A',
            $order->client->lastname ?? 'N/A',
            $order->reception_date?->format('d/m/Y'),
            $order->destination,
            $order->invoice?->amountTo_pay ?? 0,
            $order->invoice_amount_paid ?? 0,
            round(($order->invoice?->amountTo_pay ?? 0) - ($order->invoice->amount_paid ?? 0), 2),
            $order->invoice?->payment_status ?? 'N/A',
            $order->invoice?->payment_method ?? 'N/A',
            $order->responsible?->name ?? 'N/A'
        ]);
    }


    public function headings(): array
    {
        return [
            'Tracking',
            'Cliente',
            'Data Recepção',
            'Destino',
            'Valor a Pagar',
            'Valor Pago',
            'Saldo em Dívida',
            'Estado Pagamento',
            'Método Pagamento',
            'Referência',
            'Responsável',
        ];
    }

    public function title(): string
    {
        return 'relatório financeiro';
    }

    #[Override]
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
