<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
<<<<<<< HEAD
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExceptionSummarySheet implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
=======
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExceptionSummarySheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    // Brand colours
    private const PURPLE = '962479';
    private const LIME = 'C5D22D';
    private const WHITE = 'FFFFFF';
    private const RED = 'C62828';
    private const GREEN = '2E7D32';
    private const LIGHT = 'F9F0F6';
    private const GREY = 'F5F5F5';

>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
    public function __construct(private readonly array $summary) {}

    public function array(): array
    {
        return [
<<<<<<< HEAD
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
=======
            
            ['PORTADOR DIÁRIO - RELATÓRIO DE EXCEPÇÕES', '', ''],

            ['Resumo Geral', '', ''],


            ['', '', ''],

            
            ['Indicador', 'Total'],

            
            ['Encomendas sem Cliente', $this->summary['total_without_client']],
            ['Encomendas sem Estado', $this->summary['total_without_status']],
            ['Encomendas em Atraso', $this->summary['total_delayed']],

            // Row 12 - blank
            ['', '', ''],
             ['', '', ''],
              ['', '', ''],
               ['', '', ''],

            // Row 13 - quality header
            ['Índice de Qualidade Operacional', '', ''],

            // Row 14 - quality header
            ['Indicador', 'Valor'],

            // Rows 15-16 - quality data
            ['Pontuacção (0 – 4.00)', number_format($this->summary['quality_score'], 2), ''],
            ['Classificação', $this->summary['quality_label'], ''],
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
        ];
    }

    public function title(): string
    {
        return 'Resumo';
    }

<<<<<<< HEAD
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 13]],
            3 => ['font' => ['bold' => true]],
=======
    public function columnWidths(): array
    {
        return ['A' => 38, 'B' => 14, 'C' => 20];
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // == Row 1: Title ==
                $sheet->mergeCells('A1:C1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => self::WHITE]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::PURPLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(28);

                // == Row 2: Subtitle ==
                $sheet->mergeCells('A2:C2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => self::WHITE]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::LIME]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(20);

                // == Row 4: Table header ==
                $sheet->getStyle('A4:C4')->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => self::WHITE]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::PURPLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
                ]);

                // == Rows 5-10: Data rows ==
                for ($row = 5; $row <= 10; $row++) {
                    $bg = ($row % 2 === 0) ? self::LIGHT : self::WHITE;
                    $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                        'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'EEEEEE']]],
                    ]);
                    // Centre column B (numbers)
                    $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Colour column C based on value
                    $val = $sheet->getCell("B{$row}")->getValue();
                    $colour = ($val > 0) ? self::RED : self::GREEN;
                    $sheet->getStyle("C{$row}")->applyFromArray([
                        'font'      => ['bold' => true, 'color' => ['rgb' => $colour]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }

                // == Row 13: Quality section header ==
                $sheet->mergeCells('A13:C13');
                $sheet->getStyle('A13')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => self::WHITE]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::PURPLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension(13)->setRowHeight(20);

                // == Row 14: Quality sub-header ==
                $sheet->getStyle('A14:B14')->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => self::WHITE]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::LIME]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
                ]);

                // == Rows 15-16: Quality data ==
                foreach ([15, 16] as $row) {
                    $bg = ($row % 2 === 0) ? self::LIGHT : self::WHITE;
                    $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                        'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'EEEEEE']]],
                    ]);
                    $sheet->getStyle("B{$row}")->applyFromArray([
                        'font'      => ['bold' => true, 'color' => ['rgb' => self::PURPLE]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }
            },
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
        ];
    }
}