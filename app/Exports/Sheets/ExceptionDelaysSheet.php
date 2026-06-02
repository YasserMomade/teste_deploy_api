<?php

namespace App\Exports\Sheets;

use Carbon\Carbon;
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

class ExceptionDelaysSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    private const PURPLE = '962479';
    private const LIME   = 'C5D22D';
    private const WHITE  = 'FFFFFF';
    private const RED    = 'C62828';
    private const LIGHT  = 'F9F0F6';

    public function __construct(private readonly array $delays) {}

    public function title(): string { return 'Atrasos'; }

    public function columnWidths(): array
    {
        return ['A' => 18, 'B' => 22, 'C' => 18, 'D' => 22, 'E' => 18, 'F' => 18, 'G' => 14];
    }

    public function array(): array
    {
        $headers = ['Tracking', 'Cliente', 'Loja', 'Responsável', 'Saída Prevista', 'Prazo Limite', 'Horas Atraso'];

        $rows = [];
        $rows[] = ['EM TRÂNSITO E ATRASADAS'];
        $rows[] = $headers;

        if (empty($this->delays['delayed'])) {
            $rows[] = ['Sem dados'];
        } else {
            foreach ($this->delays['delayed'] as $row) {
                $rows[] = [
                    $row['tracking'],
                    $row['client'],
                    $row['store'] ?? '-',
                    $row['responsible'],
                    $row['analysis']['actual_departure_at'] ? Carbon::parse($row['analysis']['actual_departure_at'])->format('d/m/Y H:i') : '-',
                    $row['analysis']['deadline_at']         ? Carbon::parse($row['analysis']['deadline_at'])->format('d/m/Y H:i')         : '-',
                    '+' . $row['analysis']['delay_hours'] . 'h',
                ];
            }
        }

        $rows[] = [];
        $rows[] = ['CHEGARAM EM ATRASO'];
        $rows[] = ['Tracking', 'Cliente', 'Loja', 'Responsável', 'Prazo Limite', 'Chegou em', 'Horas Atraso'];

        if (empty($this->delays['arrived_late'])) {
            $rows[] = ['Sem dados'];
        } else {
            foreach ($this->delays['arrived_late'] as $row) {
                $rows[] = [
                    $row['tracking'],
                    $row['client'],
                    $row['store'] ?? '-',
                    $row['responsible'],
                    $row['analysis']['deadline_at']  ? Carbon::parse($row['analysis']['deadline_at'])->format('d/m/Y H:i')  : '-',
                    $row['analysis']['delivered_at'] ? Carbon::parse($row['analysis']['delivered_at'])->format('d/m/Y H:i') : '-',
                    '+' . $row['analysis']['delay_hours'] . 'h',
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

                for ($row = 1; $row <= $totalRows; $row++) {
                    $val = $sheet->getCell("A{$row}")->getValue();

                    if (in_array($val, ['EM TRÂNSITO E ATRASADAS', 'CHEGARAM EM ATRASO'])) {
                        $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                        $sheet->getStyle("A{$row}")->applyFromArray([
                            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => self::WHITE]],
                            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::PURPLE]],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);
                        $sheet->getRowDimension($row)->setRowHeight(22);
                        continue;
                    }

                    if ($val === 'Tracking') {
                        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                            'font'      => ['bold' => true, 'color' => ['rgb' => self::WHITE]],
                            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::LIME]],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
                        ]);
                        continue;
                    }

                    if ($val === '') continue;

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