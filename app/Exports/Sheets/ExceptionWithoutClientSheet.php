<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExceptionWithoutClientSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, ShouldAutoSize
{
    public function __construct(private readonly mixed $orders) {}

    public function collection()
    {
        return collect($this->orders)->map(fn($order) => [
            $order->tracking,
            $order->destination,
            $order->reception_date?->format('d/m/Y'),
            $order->weight,
            $order->store?->name ?? '—',
            $order->responsible?->name ?? '—',
        ]);
    }

    public function headings(): array
    {
        return ['Tracking', 'Destino', 'Data Recepção', 'Peso (kg)', 'Loja', 'Responsável'];
    }

    public function title(): string
    {
        return 'Sem Cliente';
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}