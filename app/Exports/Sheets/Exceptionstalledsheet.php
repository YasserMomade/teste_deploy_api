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

class ExceptionStalledSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    private const PURPLE = '962479';
    private const LIME   = 'C5D22D';
    private const WHITE  = 'FFFFFF';
    private const RED    = 'C62828';
    private const LIGHT  = 'F9F0F6';

    public function __construct(private readonly array $stalled) {}

    public function title(): string { return 'Paradas'; }

    public function columnWidths(): array
    {
        return ['A' => 18, 'B' => 22, 'C' => 18, 'D' => 22, 'E' => 22, 'F' => 18, 'G' => 12];
    }

    public function array(): array
    {
        $rows   = [];
        $rows[] = ['ENCOMENDAS PARADAS (+7 DIAS SEM ACTUALIZAÇÃO)'];
        $rows[] = ['Tracking', 'Cliente', 'Loja', 'Responsável', 'Último Estado', 'Última Actualização', 'Dias Parada'];

        if (empty($this->stalled)) {
            $rows[] = ['Sem dados'];
        } else {
            foreach ($this->stalled as $row) {
                $rows[] = [
                    $row['tracking'],
                    $row['client'],
                    $row['store'],
                    $row['responsible'],
                    $row['last_status'],
                    $row['last_update'],
                    $row['days_stalled'] . ' dias',
                ];
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
                $lastCol   = 'G';
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

                for ($row = 3; $row <= $totalRows; $row++) {
                    if ($sheet->getCell("A{$row}")->getValue() === '') continue;
                    $bg = ($row % 2 === 0) ? self::LIGHT : self::WHITE;
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                        'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'EEEEEE']]],
                    ]);
                    $sheet->getStyle("G{$row}")->applyFromArray([
                        'font'      => ['bold' => true, 'color' => ['rgb' => self::RED]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }
            },
        ];
    }
}