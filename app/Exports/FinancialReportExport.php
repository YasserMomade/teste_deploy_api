<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;


// === Sheet 1: Financial Summary =======

class FinancialSummarySheet implements FromArray, WithTitle, WithColumnWidths, WithEvents
{
    private const PURPLE = '962479';
    private const LIME   = 'C5D22D';
    private const WHITE  = 'FFFFFF';
    private const LIGHT  = 'F9F0F6';
    private const GREEN  = '2E7D32';
    private const RED    = 'C62828';

    private array $sectionHeaderRows = [];
    private array $tableHeaderRows = [];
    private array $kpiRows = [];
    private array $paidRows = [];
    private array $pendentRows = [];
    private array $dailyRows = [];
    private int   $accentRow = 0;

    public function __construct(
        private readonly array $summary,
        private readonly array $byPaymentStatus,
        private readonly array $byPaymentMethod,
        private readonly array $dailyTotals,
        private readonly array $filters,
    ) {}

    private function translateStatus(?string $status): string
{
    return match (strtolower($status ?? '')) {
        'paid' => 'Pago',
        'pendent' => 'Pendente',
        'failed' => 'Falhado',
        default => $status ? ucfirst($status) : '-',
    };
}

private function translatePaymentMethod(?string $method): string
{
    return match (strtolower($method ?? '')) {
        'cash' => 'Dinheiro',
        'card' => 'Cartão',
        'undefined' => 'Sem pagamentos',
        null => '',
        default => ucfirst($method),
    };
}

    public function array(): array
    {
        $rows = [];
        $rowNum = 1;

        // Title
        $this->sectionHeaderRows[] = $rowNum;
        $rows[] = ['PORTADOR DIÁRIO - RELATÓRIO FINANCEIRO', '', '', ''];
        $rowNum++;

        $period = ($this->filters['date_from'] ?? 'Início') . ' a ' . ($this->filters['date_to'] ?? now()->format('d/m/Y'));
        $rows[] = ['Período: ' . $period . '  |  Gerado em: ' . now()->format('d/m/Y H:i'), '', '', ''];
        $rowNum++;

        $rows[] = ['', '', '', ''];
        $rowNum++;

        
        $this->sectionHeaderRows[] = $rowNum;
        $rows[] = ['RESUMO FINANCEIRO', '', '', ''];
        $rowNum++;

        $this->tableHeaderRows[] = $rowNum;
        $rows[] = ['Indicador', 'Valor', '', ''];
        $rowNum++;

        $kpis = [
            ['Total a Cobrar',       number_format($this->summary['total_to_pay'], 2)],
            ['Total Cobrado',        number_format($this->summary['total_paid'], 2)],
            ['Total em Dívida',      number_format($this->summary['total_debt'], 2)],
            ['Encomendas Pagas',     $this->summary['total_paid_orders']],
            ['Encomendas Pendentes', $this->summary['total_pendent_orders']],
        ];

        foreach ($kpis as $kpi) {
            $this->kpiRows[] = $rowNum;
            $rows[] = [$kpi[0], $kpi[1], '', ''];
            $rowNum++;
        }

        $rows[] = ['', '', '', ''];
        $rowNum++;

        // By payment status
        if (! empty($this->byPaymentStatus)) {
            $this->sectionHeaderRows[] = $rowNum;
            $rows[] = ['POR ESTADO DE PAGAMENTO', '', '', ''];
            $rowNum++;

            $this->tableHeaderRows[] = $rowNum;
            $rows[] = ['Estado', 'Nº Encomendas', 'Total a Cobrar', 'Total Pago'];
            $rowNum++;

            foreach ($this->byPaymentStatus as $r) {
                $status = strtolower($r['status'] ?? '');
                if ($status === 'paid') {
                    $this->paidRows[] = $rowNum;
                } elseif ($status === 'pendent') {
                    $this->pendentRows[] = $rowNum;
                }
                $rows[] = [
                    $this->translateStatus($r['status'] ?? null),
                    $r['total_orders'],
                    number_format($r['total_amount'], 2),
                    number_format($r['total_paid'], 2),
                ];
                $rowNum++;
            }

            $rows[] = ['', '', '', ''];
            $rowNum++;
        }

        // By payment method
       if (! empty($this->byPaymentMethod)) {
    $this->sectionHeaderRows[] = $rowNum;
    $rows[] = ['POR MÉTODO DE PAGAMENTO', '', '', ''];
    $rowNum++;

    $this->tableHeaderRows[] = $rowNum;
    $rows[] = ['Método', 'Nº Encomendas', 'Total Cobrado', ''];
    $rowNum++;

    foreach ($this->byPaymentMethod as $r) {

        $method = strtolower($r['method'] ?? '');

        // Ignora métodos undefined, null ou vazios
        if (in_array($method, ['undefined', '', 'null'])) {
            continue;
        }

        $rows[] = [
            $this->translatePaymentMethod($r['method'] ?? null),
            $r['total_orders'],
            number_format($r['total_collected'], 2),
            ''
        ];

        $rowNum++;
    }

            $rows[] = ['', '', '', ''];
            $rowNum++;
        }

        // Daily totals
        if (! empty($this->dailyTotals)) {
            $this->sectionHeaderRows[] = $rowNum;
            $rows[] = ['TOTAIS DIÁRIOS', '', '', ''];
            $rowNum++;

            $this->tableHeaderRows[] = $rowNum;
            $rows[] = ['Data', 'Nº Encomendas', 'Total a Cobrar', 'Total Pago'];
            $rowNum++;

            foreach ($this->dailyTotals as $r) {
                $this->dailyRows[] = $rowNum;
                $rows[] = [
                    \Carbon\Carbon::parse($r['date'])->format('d/m/Y'),
                    $r['total_orders'],
                    number_format($r['total_to_pay'], 2),
                    number_format($r['total_paid'], 2),
                ];
                $rowNum++;
            }
        }

        $this->accentRow = $rowNum;
        $rows[] = ['', '', '', ''];

        return $rows;
    }

    public function title(): string { return 'Resumo Financeiro'; }

    public function columnWidths(): array
    {
        return ['A' => 28, 'B' => 18, 'C' => 18, 'D' => 18];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Section headers
                foreach ($this->sectionHeaderRows as $i => $row) {
                    $sheet->mergeCells("A{$row}:D{$row}");
                    if ($i === 0) {
                        // First = big title
                        $sheet->getStyle("A{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => self::WHITE]],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::PURPLE]],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);
                        $sheet->getRowDimension($row)->setRowHeight(28);
                    } else {
                        $sheet->getStyle("A{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => self::WHITE]],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::PURPLE]],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
                        ]);
                        $sheet->getRowDimension($row)->setRowHeight(22);
                    }
                }

                // Row 2 - subtitle
                $sheet->mergeCells('A2:D2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '3E2723']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::LIME]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(18);

                // Table headers - lime
                foreach ($this->tableHeaderRows as $row) {
                    $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => '3E2723'], 'size' => 9],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::LIME]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BBBBBB']]],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(20);
                }

                
                foreach ($this->kpiRows as $i => $row) {
                    $bg = ($i % 2 === 0) ? self::LIGHT : 'FFFFFF';
                    $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'EEEEEE']]],
                    ]);
                    $label = $sheet->getCell("A{$row}")->getValue();
                    $colour = match($label) {
                        'Total Cobrado' => self::GREEN,
                        'Total em Dívida' => self::RED,
                        'Encomendas Pagas' => self::GREEN,
                        default => self::PURPLE,
                    };
                    $sheet->getStyle("B{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => $colour]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(18);
                }

                // Paid rows - green tint
                foreach ($this->paidRows as $row) {
                    $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F5E9']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C8E6C9']]],
                        'font' => ['color' => ['rgb' => self::GREEN]],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(18);
                }

                // Pendent rows - amber tint
                foreach ($this->pendentRows as $row) {
                    $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF8E1']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFE082']]],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(18);
                }

                // Daily rows - alternating
                foreach ($this->dailyRows as $i => $row) {
                    $bg = ($i % 2 === 0) ? self::LIGHT : 'FFFFFF';
                    $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'EEEEEE']]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(18);
                }
            },
        ];
    }
}

// === Sheet 2: Financial Detail =======

class FinancialDetailSheet implements FromCollection, WithTitle, WithHeadings, WithColumnWidths, WithEvents
{
    private const PURPLE = '962479';
    private const LIME = 'C5D22D';
    private const WHITE = 'FFFFFF';
    private const LIGHT = 'F9F0F6';
    private const GRN_BG = 'E8F5E9';
    private const AMB_BG = 'FFF8E1';
    private const RED_BG = 'FFEBEE';

    private function translateStatus(?string $status): string
{
    return match (strtolower($status ?? '')) {
        'paid' => 'Pago',
        'pendent' => 'Pendente',
        'failed' => 'Falhado',
        default => $status ? ucfirst($status) : '-',
    };
}

private function translatePaymentMethod(?string $method): string
{
    return match (strtolower($method ?? '')) {
        'cash' => 'Dinheiro',
        'mpesa' => 'M-Pesa',
        'emola' => 'E-Mola',
        'bank_transfer' => 'Transferência Bancária',
        'card' => 'Cartão',
        'undefined', '', null => '',
        default => ucfirst($method),
    };
}

    private array $rowStatuses = [];

    public function __construct(private readonly mixed $orders) {}

    public function collection()
    {
        $rows = collect($this->orders)->map(function ($order) {
            $status = $order->invoice?->payment_status ?? 'pendent';
            $this->rowStatuses[] = $status;

            return [
                $order->tracking,
                trim(($order->client?->name ?? '') . ' ' . ($order->client?->lastname ?? '')) ?: '-',
                $order->created_at?->format('d/m/Y') ?? '-',
                number_format($order->invoice?->amountTo_pay ?? 0, 2),
                number_format($order->invoice?->amount_paid ?? 0, 2),
                number_format(($order->invoice?->amountTo_pay ?? 0) - ($order->invoice?->amount_paid ?? 0), 2),
                $this->translateStatus($status),
                $this->translatePaymentMethod($order->invoice?->payment_method),
                $order->invoice?->referencie ?? '-',
                trim(($order->responsible?->name ?? '') . ' ' . ($order->responsible?->lastname ?? '')) ?: '-',
            ];
        });

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Tracking', 'Cliente', 'Data',
            'A Pagar', 'Pago', 'Saldo', 'Estado Pagamento',
            'Método', 'Referência', 'Responsável',
        ];
    }

    public function title(): string { return 'Detalhe Financeiro'; }

    public function columnWidths(): array
    {
        return [
            'A' => 20, 'B' => 26, 'C' => 14,
            'D' => 14, 'E' => 12, 'F' => 12, 'G' => 18,
            'H' => 14, 'I' => 20, 'J' => 22,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Header
                $sheet->getStyle('A1:J1')->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => self::WHITE], 'size' => 9],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::PURPLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BBBBBB']]],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->freezePane('A2');

                // Data rows - coloured by payment status
                foreach ($this->rowStatuses as $i => $status) {
                    $row = $i + 2;
                    $bg  = match($status) {
                        'paid' => self::GRN_BG,
                        'pendent' => self::AMB_BG,
                        'faild' => self::RED_BG,
                        default => ($i % 2 === 0) ? self::LIGHT : 'FFFFFF',
                    };
                    $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'EEEEEE']]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    // Saldo column G
                    $sheet->getStyle("D{$row}:F{$row}")->getAlignment()
                          ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getRowDimension($row)->setRowHeight(18);
                }

                // Outer border
                $sheet->getStyle("A1:J{$lastRow}")->applyFromArray([
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::PURPLE]]],
                ]);

               
            },
        ];
    }
}

// === Main Export =====

class FinancialReportExport implements WithMultipleSheets
{
    public function __construct(private readonly array $data) {}

    public function sheets(): array
    {
        return [
            new FinancialSummarySheet(
                $this->data['summary'],
                $this->data['by_payment_status'],
                $this->data['by_payment_method'],
                $this->data['daily_totals'],
                $this->data['filters_applied'],
            ),
            new FinancialDetailSheet($this->data['orders']),
        ];
    }
}