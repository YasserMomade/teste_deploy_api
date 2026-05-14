<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class OperationalSummarySheet implements FromArray, WithTitle, WithColumnWidths, WithEvents
{
    private const PURPLE = '962479';
    private const LIME   = 'C5D22D';
    private const WHITE  = 'FFFFFF';
    private const LIGHT  = 'F9F0F6';

    private array $sectionHeaderRows = [];
    private array $tableHeaderRows = [];
    private int $accentRow = 0;

    public function __construct(
        private readonly array $summary,
        private readonly array $byDestination,
        private readonly array $byStatus,
        private readonly array $byServiceType,
        private readonly array $byCategory,
        private readonly array $byStore,
        private readonly array $filters,
    ) {}

    public function array(): array
    {
        $rows = [];
        $rowNum = 1;

        // ===== Main Title =====
        $this->sectionHeaderRows[] = $rowNum;
        $rows[] = ['PORTADOR DIÁRIO - RELATÓRIO OPERACIONAL', '', ''];
        $rowNum++;

        // ===== Subtitle =====
        $period = ($this->filters['date_from'] ?? 'Início') .
            ' a ' .
            ($this->filters['date_to'] ?? 'Hoje');

        $rows[] = [
            'Período: ' . $period .
            '  |  Gerado em: ' .
            now()->format('d/m/Y H:i'),
            '',
            ''
        ];
        $rowNum++;

        $rows[] = ['', '', ''];
        $rowNum++;

        // ===== Summary =====
        $this->sectionHeaderRows[] = $rowNum;
        $rows[] = ['RESUMO GERAL', '', ''];
        $rowNum++;

        $this->tableHeaderRows[] = $rowNum;
        $rows[] = ['Indicador', 'Total', ''];
        $rowNum++;

        $summaryRows = [
            ['Total de Encomendas', $this->summary['total_orders']],
            ['Total de Volumes', $this->summary['total_volumes']],
            ['Peso Real (kg)', number_format($this->summary['total_weight'], 3)],
            ['Peso Taxado (kg)', number_format($this->summary['total_declared_weight'], 3)],
        ];

        foreach ($summaryRows as $item) {
            $rows[] = [$item[0], $item[1], ''];
            $rowNum++;
        }

        $rows[] = ['', '', ''];
        $rowNum++;

        // ===== By Destination =====
        if (!empty($this->byDestination)) {

            $this->sectionHeaderRows[] = $rowNum;
            $rows[] = ['POR DESTINO', '', ''];
            $rowNum++;

            $this->tableHeaderRows[] = $rowNum;
            $rows[] = ['Destino', 'Encomendas', 'Peso Total (kg)'];
            $rowNum++;

            foreach ($this->byDestination as $r) {
                $rows[] = [
                    $r['destination'],
                    $r['total_orders'],
                    number_format($r['total_weight'], 3)
                ];

                $rowNum++;
            }

            $rows[] = ['', '', ''];
            $rowNum++;
        }

        // ===== By Status =====
        if (!empty($this->byStatus)) {

            $this->sectionHeaderRows[] = $rowNum;
            $rows[] = ['POR ESTADO ACTUAL', '', ''];
            $rowNum++;

            $this->tableHeaderRows[] = $rowNum;
            $rows[] = ['Estado', 'Total', ''];
            $rowNum++;

            foreach ($this->byStatus as $r) {

                $rows[] = [
                    str_replace('_', ' ', ucfirst($r['status'])),
                    $r['total'],
                    ''
                ];

                $rowNum++;
            }

            $rows[] = ['', '', ''];
            $rowNum++;
        }

        // ===== By Service Type =====
        if (!empty($this->byServiceType)) {

            $this->sectionHeaderRows[] = $rowNum;
            $rows[] = ['POR TIPO DE SERVIÇO', '', ''];
            $rowNum++;

            $this->tableHeaderRows[] = $rowNum;
            $rows[] = ['Tipo de Serviço', 'Total', ''];
            $rowNum++;

            foreach ($this->byServiceType as $r) {

                $rows[] = [
                    $r['service_type'],
                    $r['total'],
                    ''
                ];

                $rowNum++;
            }

            $rows[] = ['', '', ''];
            $rowNum++;
        }

        // ===== By Category =====
        if (!empty($this->byCategory)) {

            $this->sectionHeaderRows[] = $rowNum;
            $rows[] = ['POR CATEGORIA', '', ''];
            $rowNum++;

            $this->tableHeaderRows[] = $rowNum;
            $rows[] = ['Categoria', 'Total', ''];
            $rowNum++;

            foreach ($this->byCategory as $r) {

                $rows[] = [
                    $r['category'],
                    $r['total'],
                    ''
                ];

                $rowNum++;
            }

            $rows[] = ['', '', ''];
            $rowNum++;
        }

        // ===== By Store =====
        if (!empty($this->byStore)) {

            $this->sectionHeaderRows[] = $rowNum;
            $rows[] = ['POR LOJA', '', ''];
            $rowNum++;

            $this->tableHeaderRows[] = $rowNum;
            $rows[] = ['Loja', 'Total', ''];
            $rowNum++;

            foreach ($this->byStore as $r) {

                $rows[] = [
                    $r['store'],
                    $r['total'],
                    ''
                ];

                $rowNum++;
            }
        }

        // ===== Accent Line =====
        $this->accentRow = $rowNum + 1;

        $rows[] = ['', '', ''];

        return $rows;
    }

    public function title(): string
    {
        return 'Resumo';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35,
            'B' => 18,
            'C' => 18,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                // ===== Section Headers =====
                foreach ($this->sectionHeaderRows as $i => $row) {

                    $sheet->mergeCells("A{$row}:C{$row}");

                    if ($i === 0) {

                        // Main title
                        $sheet->getStyle("A{$row}")->applyFromArray([
                            'font' => [
                                'bold' => true,
                                'size' => 14,
                                'color' => ['rgb' => self::WHITE]
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => self::PURPLE]
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                        ]);

                        $sheet->getRowDimension($row)->setRowHeight(28);

                    } else {

                        // Section title
                        $sheet->getStyle("A{$row}")->applyFromArray([
                            'font' => [
                                'bold' => true,
                                'size' => 11,
                                'color' => ['rgb' => self::WHITE]
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => self::PURPLE]
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_LEFT,
                                'vertical' => Alignment::VERTICAL_CENTER,
                                'indent' => 1
                            ],
                        ]);

                        $sheet->getRowDimension($row)->setRowHeight(22);
                    }
                }

                // ===== Subtitle =====
                $sheet->mergeCells('A2:C2');

                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'size' => 10,
                        'color' => ['rgb' => '3E2723']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => self::LIME]
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getRowDimension(2)->setRowHeight(18);

                // ===== Table Headers =====
                foreach ($this->tableHeaderRows as $row) {

                    $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => '3E2723'],
                            'size' => 9
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => self::LIME]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'BBBBBB']
                            ]
                        ],
                    ]);

                    $sheet->getRowDimension($row)->setRowHeight(20);
                }

                // ===== Data Rows =====
                $highestRow = $sheet->getHighestRow();

                for ($row = 1; $row <= $highestRow; $row++) {

                    if (
                        in_array($row, $this->sectionHeaderRows) ||
                        in_array($row, $this->tableHeaderRows) ||
                        $row === 2
                    ) {
                        continue;
                    }

                    $value = $sheet->getCell("A{$row}")->getValue();

                    if ($value !== '') {

                        $bg = ($row % 2 === 0)
                            ? self::LIGHT
                            : 'FFFFFF';

                        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => $bg]
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['rgb' => 'EEEEEE']
                                ]
                            ],
                            'alignment' => [
                                'vertical' => Alignment::VERTICAL_CENTER
                            ],
                        ]);

                        $sheet->getStyle("B{$row}")
                            ->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                        $sheet->getRowDimension($row)->setRowHeight(18);
                    }
                }

                // ===== Outer Border =====
                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle("A1:C{$highestRow}")
                    ->applyFromArray([
                        'borders' => [
                            'outline' => [
                                'borderStyle' => Border::BORDER_MEDIUM,
                                'color' => ['rgb' => self::PURPLE]
                            ]
                        ],
                    ]);
            },
        ];
    }
}


class OperationalDetailSheet implements FromCollection, WithTitle, WithHeadings, WithColumnWidths, WithEvents
{
    private const PURPLE = '962479';
    private const LIME = 'C5D22D';
    private const WHITE = 'FFFFFF';
    private const LIGHT = 'F9F0F6';

    public function __construct(private readonly mixed $orders) {}

    public function collection()
    {
        return collect($this->orders)->map(fn($order) => [
            $order->tracking,
            $order->client?->full_name ?? '-',
            $order->origin,
            $order->destination,
            $order->reception_date?->format('d/m/Y') ?? '-',
            $order->service_type,
            $order->category?->category ?? '-',
            $order->store?->name ?? '-',
            $order->volume_number,
            $order->weight,
            $order->declared_weight ?? '-',
            $order->latestStatus?->descryption
                ? str_replace('_', ' ', $order->latestStatus->descryption)
                : '-',
            $order->responsible?->full_name ?? '-',
            // Entregue por
            optional(
                $order->statuses?->where('descryption', 'entregue')->sortByDesc('created_at')->first()
            )->responsible?->full_name ?? '-',
        ]);
    }

    public function headings(): array
    {
        return [
            'Tracking', 'Cliente', 'Origem', 'Destino', 'Data Recepção',
            'Tipo Serviço', 'Categoria', 'Loja', 'Volumes',
            'Peso (kg)', 'Peso Taxado (kg)', 'Estado Actual',
            'Responsável', 'Entregue Por',
        ];
    }

    public function title(): string { return 'Encomendas'; }

    public function columnWidths(): array
    {
        return [
            'A' => 20, 'B' => 26, 'C' => 14, 'D' => 16, 'E' => 14,
            'F' => 14, 'G' => 16, 'H' => 18, 'I' => 8,
            'J' => 10, 'K' => 14, 'L' => 20, 'M' => 20, 'N' => 20,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Header
                $sheet->getStyle('A1:N1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => self::WHITE], 'size' => 9],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::PURPLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BBBBBB']]],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->freezePane('A2');

                // Data rows
                for ($row = 2; $row <= $lastRow; $row++) {
                    $bg = ($row % 2 === 0) ? self::LIGHT : 'FFFFFF';
                    $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'EEEEEE']]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(18);
                }

                // Outer border
                $sheet->getStyle("A1:N{$lastRow}")->applyFromArray([
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::PURPLE]]],
                ]);

        
            },
        ];
    }
}
// ====== Main Export ====================

class OperationalReportExport implements WithMultipleSheets
{
    public function __construct(private readonly array $data) {}

    public function sheets(): array
    {
        return [
            new OperationalSummarySheet(
                $this->data['summary'],
                $this->data['by_destination'],
                $this->data['by_status'],
                $this->data['by_service_type'],
                $this->data['by_category'],
                $this->data['by_store'],
                $this->data['filters_applied'],
            ),
            new OperationalDetailSheet($this->data['orders']),
        ];
    }
}
