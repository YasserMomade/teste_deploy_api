<?php

namespace App\Exports\Sheets;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExceptionQualitySheet implements FromArray, WithTitle, WithColumnWidths, WithEvents
{
    private const PURPLE = '962479';
    private const LIME = 'C5D22D';
    private const WHITE = 'FFFFFF';
    private const LIGHT = 'F9F0F6';
    private const GRN_BG = 'E8F5E9';
    private const GRN_FG = '1B5E20';
    private const RED_BG = 'FFEBEE';
    private const RED_FG = 'B71C1C';
    private const AMB_BG = 'FFF8E1';
    private const AMB_FG = '6D4C41';
    private const DARK_RED = 'e3c1c6';

    private array $sectionHeaderRows = [];
    private array $tableHeaderRows = [];
    private array $scoreRows = [];
    private array $responsibleRows = [];
    private array $criticalObsRows = [];
    private array $trendRows = [];
    private array $obsHeaderRows = [];
    private int   $accentRow = 0;

    public function __construct(private readonly array $quality) {}

    public function array(): array
    {
        $rows = [];
        $rowNum = 1;

        // ==== Score summary ====
        $this->sectionHeaderRows[] = $rowNum;
        $rows[] = ['ÍNDICE DE QUALIDADE OPERACIONAL', '', '', '', '', '', '', '', ''];
        $rowNum++;

        $this->tableHeaderRows[] = $rowNum;
        $rows[] = ['Indicador', 'Valor', '', '', '', '', '', '', ''];
        $rowNum++;

        $scoreItems = [
            ['Score (0.00 - 4.00)', number_format($this->quality['score']['score'], 2)],
            ['Percentagem', $this->quality['score']['percentage'] . '%'],
            ['Classificação', $this->quality['score']['label']],
        ];

        foreach ($scoreItems as $item) {
            $this->scoreRows[] = $rowNum;
            $rows[] = [$item[0], $item[1], '', '', '', '', '', '', ''];
            $rowNum++;
        }

        $rows[] = ['', '', '', '', '', '', '', '', ''];
        $rowNum++;

        // ==== Observation summary ====
        $this->sectionHeaderRows[] = $rowNum;
        $rows[] = ['RESUMO DE OBSERVAÇÕES', '', '', '', '', '', '', '', ''];
        $rowNum++;

        $this->tableHeaderRows[] = $rowNum;
        $rows[] = ['Nível', 'Total', 'Percentagem', '', '', '', '', '', ''];
        $rowNum++;

        $levels = [
            ['Good', $this->quality['summary']['total_good'], 'good'],
            ['Medium', $this->quality['summary']['total_medium'],'medium'],
            ['Bad', $this->quality['summary']['total_bad'], 'bad'],
            ['Critical', $this->quality['summary']['total_critical'], 'critical'],
        ];

        $total = max($this->quality['summary']['total_observations'], 1);
        foreach ($levels as [$label, $count, $level]) {
            $pct = round($count / $total * 100, 1);
            $this->obsHeaderRows[] = ['row' => $rowNum, 'level' => $level];
            $rows[] = [$label, $count, $pct . '%', '', '', '', '', '', ''];
            $rowNum++;
        }

        // Total row
        $rows[] = ['TOTAL', $this->quality['summary']['total_observations'], '100%', '', '', '', '', '', ''];
        $this->tableHeaderRows[] = $rowNum;
        $rowNum++;

        $rows[] = ['', '', '', '', '', '', '', '', ''];
        $rowNum++;

        // // ==== By responsible =======
        // if (! empty($this->quality['by_responsible'])) {
        //     $this->sectionHeaderRows[] = $rowNum;
        //     $rows[] = ['QUALIDADE POR COLABORADOR', '', '', '', '', '', '', '', ''];
        //     $rowNum++;

        //     $this->tableHeaderRows[] = $rowNum;
        //     $rows[] = ['Colaborador', 'Código', 'Total', 'Good', 'Medium', 'Bad', 'Critical', 'Score', 'Classificação'];
        //     $rowNum++;

        //     foreach ($this->quality['by_responsible'] as $row) {
        //         $this->responsibleRows[] = ['row' => $rowNum, 'score' => $row['score']];
        //         $rows[] = [
        //             $row['responsible'],
        //             $row['user_code'],
        //             $row['total'],
        //             $row['good'],
        //             $row['medium'],
        //             $row['bad'],
        //             $row['critical'],
        //             $row['score'] . '/4',
        //             $row['score_label'],
        //         ];
        //         $rowNum++;
        //     }

        //     $rows[] = ['', '', '', '', '', '', '', '', ''];
        //     $rowNum++;
        // }

        // ==== Critical and bad orders =====
        if (! empty($this->quality['critical_and_bad_orders'])) {
            $this->sectionHeaderRows[] = $rowNum;
            $rows[] = ['ENCOMENDAS COM OBSERVAÇÕES CRÍTICAS OU MÁS', '', '', '', '', '', '', '', ''];
            $rowNum++;

            foreach ($this->quality['critical_and_bad_orders'] as $order) {
                // Order header row
                $this->obsHeaderRows[] = ['row' => $rowNum, 'level' => 'order_header'];
                $rows[] = [
                    $order['tracking'],
                    $order['client'],
                    $order['destination'],
                    $order['service_type'],
                    'Resp: ' . $order['responsible'],
                    '', '', '', '',
                ];
                $rowNum++;

                // Observation sub-header
                $this->tableHeaderRows[] = $rowNum;
                $rows[] = ['Nível', 'Descrição', 'Registado por', 'Data', '', '', '', '', ''];
                $rowNum++;

                foreach ($order['observations'] as $obs) {
                    $this->criticalObsRows[] = ['row' => $rowNum, 'level' => $obs['level']];
                    $rows[] = [
                        ucfirst($obs['level']),
                        $obs['description'],
                        $obs['created_by'],
                        $obs['created_at'],
                        '', '', '', '', '',
                    ];
                    $rowNum++;
                }

                $rows[] = ['', '', '', '', '', '', '', '', ''];
                $rowNum++;
            }
        }

        // === Trend =====
        if (! empty($this->quality['trend'])) {
            $this->sectionHeaderRows[] = $rowNum;
            $rows[] = ['TENDÊNCIA DE QUALIDADE POR DIA', '', '', '', '', '', '', '', ''];
            $rowNum++;

            $this->tableHeaderRows[] = $rowNum;
            $rows[] = ['Data', 'Good', 'Medium', 'Bad', 'Critical', '', '', '', ''];
            $rowNum++;

            foreach ($this->quality['trend'] as $row) {
                $this->trendRows[] = $rowNum;
                $rows[] = [
                    Carbon::parse($row['date'])->format('d/m/Y'),
                    $row['good'],
                    $row['medium'],
                    $row['bad'],
                    $row['critical'],
                    '', '', '', '',
                ];
                $rowNum++;
            }

            $rows[] = ['', '', '', '', '', '', '', '', ''];
            $rowNum++;
        }

        $this->accentRow = $rowNum;
        $rows[] = ['', '', '', '', '', '', '', '', ''];

        return $rows;
    }

    public function title(): string
    {
        return 'Qualidade';
    }

    public function columnWidths(): array
    {
        return ['A' => 28, 'B' => 28, 'C' => 20, 'D' => 20, 'E' => 14, 'F' => 10, 'G' => 10, 'H' => 12, 'I' => 16];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Section headers
                foreach ($this->sectionHeaderRows as $row) {
                    $sheet->mergeCells("A{$row}:I{$row}");
                    $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => self::WHITE]],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::PURPLE]],
                        'alignment'=> ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(22);
                }

                // Table headers - lime
                foreach ($this->tableHeaderRows as $row) {
                    $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => '3E2723'], 'size' => 9],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::LIME]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BBBBBB']]],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(20);
                }

                // Score rows - purple text, light bg
                foreach ($this->scoreRows as $row) {
                    $bg = ($row % 2 === 0) ? self::LIGHT : self::WHITE;
                    $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'EEEEEE']]],
                    ]);
                    $sheet->getStyle("B{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => self::PURPLE]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(18);
                }

                // Observation level rows
                foreach ($this->obsHeaderRows as $item) {
                    $row = $item['row'];
                    $level = $item['level'];

                    if ($level === 'order_header') {
                        $sheet->mergeCells("A{$row}:I{$row}");
                        $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['rgb' => self::WHITE]],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '6B1A50']],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
                        ]);
                        $sheet->getRowDimension($row)->setRowHeight(18);
                        continue;
                    }

                    [$bg, $fg] = match($level) {
                        'good' => [self::GRN_BG, self::GRN_FG],
                        'medium' => [self::AMB_BG, self::AMB_FG],
                        'bad' => [self::RED_BG, self::RED_FG],
                        'critical' => [self::DARK_RED, self::WHITE],
                        default => [self::WHITE, '333333'],
                    };

                    $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'EEEEEE']]],
                    ]);
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => $fg]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(18);
                }

                // Critical obs rows - colour by level
                foreach ($this->criticalObsRows as $item) {
                    $row   = $item['row'];
                    $level = $item['level'];

                    [$bg, $fg] = match($level) {
                        'good' => [self::GRN_BG, self::GRN_FG],
                        'medium' => [self::AMB_BG, self::AMB_FG],
                        'bad' => [self::RED_BG, self::RED_FG],
                        'critical' => [self::DARK_RED, self::WHITE],
                        default => [self::WHITE, '333333'],
                    };

                    $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'EEEEEE']]],
                    ]);
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'font'=> ['bold' => true, 'color' => ['rgb' => $fg]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(18);
                }

                // Responsible rows - score-based colour
                foreach ($this->responsibleRows as $item) {
                    $row  = $item['row'];
                    $score = $item['score'];

                    $bg = match(true) {
                        $score >= 3.5 => self::GRN_BG,
                        $score >= 2.5 => self::AMB_BG,
                        default => self::RED_BG,
                    };

                    $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'EEEEEE']]],
                    ]);
                    $sheet->getStyle("H{$row}")->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(18);
                }

                // Trend rows - alternating
                foreach ($this->trendRows as $i => $row) {
                    $bg = ($i % 2 === 0) ? self::LIGHT : self::WHITE;
                    $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'EEEEEE']]],
                    ]);
                    $sheet->getStyle("B{$row}")->getFont()->getColor()->setRGB(self::GRN_FG);
                    $sheet->getStyle("C{$row}")->getFont()->getColor()->setRGB(self::AMB_FG);
                    $sheet->getStyle("D{$row}")->getFont()->getColor()->setRGB(self::RED_FG);
                    $sheet->getStyle("E{$row}")->getFont()->getColor()->setRGB(self::DARK_RED);
                    $sheet->getRowDimension($row)->setRowHeight(18);
                }

                // Accent bottom line
                if ($this->accentRow > 0) {
                    $sheet->getStyle("A{$this->accentRow}:I{$this->accentRow}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::LIME]],
                    ]);
                    $sheet->getRowDimension($this->accentRow)->setRowHeight(4);
                }
            },
        ];
    }
}