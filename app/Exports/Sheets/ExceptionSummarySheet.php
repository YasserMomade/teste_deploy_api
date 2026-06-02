<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExceptionSummarySheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    private const PURPLE = '962479';
    private const LIME   = 'C5D22D';
    private const WHITE  = 'FFFFFF';
    private const RED    = 'C62828';
    private const GREEN  = '2E7D32';
    private const LIGHT  = 'F9F0F6';

    public function __construct(private readonly array $summary) {}

    public function title(): string { return 'Resumo'; }

    public function columnWidths(): array { return ['A' => 50, 'B' => 20]; }

    public function array(): array
    {
        return [
            ['PORTADOR DIÁRIO - RELATÓRIO DE EXCEPÇÕES', ''],
            ['Resumo Geral', ''],
            ['', ''],
            ['Indicador', 'Total'],
            ['Em Trânsito e Atrasadas', $this->summary['total_in_transit_delayed']],
            ['Chegaram em Atraso',      $this->summary['total_arrived_late']],
            ['Paradas (+7 dias)',        $this->summary['total_stalled']],
            ['Observações Críticas',    $this->summary['total_critical_obs']],
        ];
    }

    public function styles(Worksheet $sheet): array { return []; }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->mergeCells('A1:B1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => self::WHITE]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::PURPLE]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(36);

                $sheet->mergeCells('A2:B2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => self::WHITE]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::LIME]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(20);

                $sheet->getStyle('A4:B4')->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => self::WHITE]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::PURPLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
                ]);

                for ($row = 5; $row <= 8; $row++) {
                    $bg = ($row % 2 === 0) ? self::LIGHT : self::WHITE;
                    $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                        'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'EEEEEE']]],
                    ]);
                    $val    = $sheet->getCell("B{$row}")->getValue();
                    $colour = ($val > 0) ? self::RED : self::GREEN;
                    $sheet->getStyle("B{$row}")->applyFromArray([
                        'font'      => ['bold' => true, 'color' => ['rgb' => $colour]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }
            },
        ];
    }
}