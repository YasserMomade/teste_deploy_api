<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExceptionWithoutDeclaredWeightSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, ShouldAutoSize
{
    public function __construct(private readonly mixed $orders) {}

    public function collection()
    {
        return collect($this->orders)->map(fn($order) => [
            $order->tracking,
            $order->client?->full_name ?? '—',
            $order->destination,
            $order->reception_date?->format('d/m/Y'),
            $order->weight,
            $order->responsible?->name ?? '—',
        ]);
    }

    public function headings(): array
    {
        return ['Tracking', 'Cliente', 'Destino', 'Data Recepção', 'Peso Real (kg)', 'Responsável'];
    }

    public function title(): string
    {
        return 'Sem Peso Declarado';
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}