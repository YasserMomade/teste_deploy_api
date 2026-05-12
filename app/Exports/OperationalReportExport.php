<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class OperationalReportExport implements
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
            $order['client']['name'] ?? 'N/A',
            $order->origin,
            $order->destination,
            $order->reception_date?->format('d/m/Y'),
            $order->service_type,
            $order->category?->category ?? 'N/A',
            $order->store?->name ?? 'N/A',
            $order->volume_number,
            $order->weight,
            $order->declared_weight ?? 'N/A',
            $order->latestStatus?->descryption ?? 'Sem estado',
            $order->responsible?->name ?? 'N/A',
        ]);
    }

    public function headings(): array
    {
        return [
            'Tracking',
            'Cliente',
            'Origem',
            'Destino',
            'Data Recepção',
            'Tipo Serviço',
            'Categoria',
            'Loja',
            'Volumes',
            'Peso (kg)',
            'Peso Declarado (kg)',
            'Estado Actual',
            'Responsável',
        ];
    }

    public function title(): string
    {
        return 'Relatório Operacional';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
