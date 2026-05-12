<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExceptionSummarySheet implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
    public function __construct(private readonly array $summary) {}

    public function array(): array
    {
        return [
            ['Resumo Geral de Excepções', ''],
            ['', ''],
            ['Indicador', 'Total'],
            ['Encomendas sem Cliente', $this->summary['total_without_client']],
            ['Encomendas sem Factura', $this->summary['total_without_invoice']],
            ['Encomendas sem Peso Declarado', $this->summary['total_without_declared_weight']],
            ['Encomendas sem Estado', $this->summary['total_without_status']],
            ['Facturas com Estado em Falta', $this->summary['total_invoices_null_status']],
            ['Encomendas em Atraso', $this->summary['total_delayed']],
            ['', ''],
            ['Índice de Qualidade (0-4)', $this->summary['quality_score']],
            ['Classificação de Qualidade', $this->summary['quality_label']],
        ];
    }

    public function title(): string
    {
        return 'Resumo';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 13]],
            3 => ['font' => ['bold' => true]],
        ];
    }
}