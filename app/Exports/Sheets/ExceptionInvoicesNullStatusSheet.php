<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExceptionInvoicesNullStatusSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, ShouldAutoSize
{
    public function __construct(private readonly mixed $orders) {}

    public function collection()
    {
        return collect($this->orders)->map(fn($order) => [
            $order->tracking,
            $order->client?->full_name ?? '—',
            $order->destination,
            $order->reception_date?->format('d/m/Y'),
            number_format($order->invoice?->amountTo_pay ?? 0, 2),
            $order->invoice?->referencie ?? '—',
        ]);
    }

    public function headings(): array
    {
        return ['Tracking', 'Cliente', 'Destino', 'Data Recepção', 'Valor a Pagar', 'Referência'];
    }

    public function title(): string
    {
        return 'Facturas s-Estado';
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}