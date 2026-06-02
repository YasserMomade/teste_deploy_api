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

class ExceptionQualitySheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    private const PURPLE   = '962479';
    private const LIME     = 'C5D22D';
    private const WHITE    = 'FFFFFF';
    private const RED      = 'C62828';
    private const DARK_RED = '3D0000';
    private const LIGHT    = 'F9F0F6';

    public function __construct(private readonly array $quality) {}

    public function title(): string { return 'Observações'; }

    public function columnWidths(): array
    {
        return ['A' => 14, 'B' => 45, 'C' => 25, 'D' => 20];
    }

    public function array(): array
    {
        $rows = [];

        $rows[] = ['RESUMO DE OBSERVAÇÕES', '', '', ''];
        $rows[] = ['Total', 'Boas', 'Médias', 'Más', 'Críticas'];
        $rows[] = [
            $this->quality['summary']['total_observations'],
            $this->quality['summary']['total_good'],
            $this->quality['summary']['total_medium'],
            $this->quality['summary']['total_bad'],
            $this->quality['summary']['total_critical'],
        ];

        $rows[] = [];
        $rows[] = [];
        $rows[] = [];
        $rows[] = ['ENCOMENDAS COM OBSERVAÇÕES CRÍTICAS OU MÁS', '', '', ''];

        if (empty($this->quality['critical_and_bad_orders'])) {
            $rows[] = ['Sem dados'];
        } else {
            foreach ($this->quality['critical_and_bad_orders'] as $order) {
                $rows[] = ['__order__', $order['tracking'] . '  |  ' . $order['client'] . '  |  Resp: ' . $order['responsible'], '', ''];
                $rows[] = ['Nível', 'Descrição', 'Registado por', 'Data'];
                foreach ($order['observations'] as $obs) {
                    $levelPt = match(strtolower($obs['level'])) {
                        'good'     => 'Bom',
                        'medium'   => 'Médio',
                        'bad'      => 'Mau',
                        'critical' => 'Crítico',
                        default    => ucfirst($obs['level']),
                    };
                    $rows[] = [
                        $levelPt,
                        $obs['description'],
                        $obs['created_by'],
                        $obs['created_at'],
                    ];
                }
                $rows[] = [];
            }
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array { return []; }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet     = $event->sheet->getDelegate();
                $lastCol   = 'E';
                $totalRows = $sheet->getHighestRow();

                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => self::WHITE]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::PURPLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(22);

                $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => self::WHITE]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::LIME]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
                ]);

                $sheet->getStyle("A3:{$lastCol}3")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => self::PURPLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'EEEEEE']]],
                ]);

                for ($row = 4; $row <= $totalRows; $row++) {
                    $val = $sheet->getCell("A{$row}")->getValue();

                    if ($val === 'ENCOMENDAS COM OBSERVAÇÕES CRÍTICAS OU MÁS') {
                        $sheet->mergeCells("A{$row}:D{$row}");
                        $sheet->getStyle("A{$row}")->applyFromArray([
                            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => self::WHITE]],
                            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::PURPLE]],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical'   => Alignment::VERTICAL_CENTER,
                                'wrapText'   => false,
                            ],
                        ]);
                        $sheet->getRowDimension($row)->setRowHeight(22);
                        continue;
                    }

                    if ($val === 'Nível') {
                        $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                            'font'      => ['bold' => true, 'color' => ['rgb' => self::WHITE]],
                            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::LIME]],
                            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
                        ]);
                        continue;
                    }

                    if ($val === '' || $val === null) continue;

                    if ($val === '__order__') {
                        $label = $sheet->getCell("B{$row}")->getValue();
                        $sheet->setCellValue("A{$row}", $label);
                        $sheet->mergeCells("A{$row}:D{$row}");
                        $sheet->getStyle("A{$row}")->applyFromArray([
                            'font'      => ['bold' => true, 'color' => ['rgb' => self::PURPLE]],
                            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3E6F0']],
                            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E0C8DB']]],
                        ]);
                        $sheet->getStyle("B{$row}:D{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3E6F0']],
                        ]);
                        continue;
                    }

                    $levelMap = ['Crítico' => self::DARK_RED, 'Mau' => self::RED];
                    if (isset($levelMap[$val])) {
                        $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF0F0']],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'EEEEEE']]],
                        ]);
                        $sheet->getStyle("A{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['rgb' => $levelMap[$val]]],
                        ]);
                        continue;
                    }

                    $bg = ($row % 2 === 0) ? self::LIGHT : self::WHITE;
                    $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                        'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'EEEEEE']]],
                    ]);
                }
            },
        ];
    }
}