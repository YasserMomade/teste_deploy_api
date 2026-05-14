<?php

namespace App\Exports\Sheets;

<<<<<<< HEAD
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExceptionDelaysSheet implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
=======
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExceptionDelaysSheet implements FromArray, WithTitle, WithColumnWidths, WithEvents
{
    private const PURPLE = '962479';
    private const LIME = 'C5D22D';
    private const WHITE = 'FFFFFF';
    private const LIGHT = 'F9F0F6';
    private const RED_BG = 'FFEBEE';
    private const RED_FG = 'B71C1C';
    private const GRN_BG = 'E8F5E9';
    private const GRN_FG = '1B5E20';


    private array $sectionHeaderRows = [];
    private array $tableHeaderRows = [];
    private array $delayedDataRows = [];
    private array $onTimeDataRows = [];
    private int $accentRow = 0;

>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
    public function __construct(private readonly array $delays) {}

    public function array(): array
    {
        $rows = [];
<<<<<<< HEAD

      
        $rows[] = ['Resumo de Atrasos', '', '', '', '', '', '', '', '', ''];
        $rows[] = ['Total Analisadas', $this->delays['summary']['total_analysed'], '', '', '', '', '', '', '', ''];
        $rows[] = ['Em Atraso', $this->delays['summary']['total_delayed'],  '', '', '', '', '', '', '', ''];
        $rows[] = ['Dentro do Prazo', $this->delays['summary']['total_on_time'],  '', '', '', '', '', '', '', ''];
        $rows[] = ['Sem Configuração', $this->delays['summary']['total_no_config'],'', '', '', '', '', '', '', ''];
        $rows[] = ['', '', '', '', '', '', '', '', '', ''];

        if (! empty($this->delays['delayed'])) {
            $rows[] = ['ENCOMENDAS EM ATRASO', '', '', '', '', '', '', '', '', ''];
            $rows[] = [
                'Tracking', 'Cliente', 'Origem', 'Destino', 'Serviço',
                'Saída Prevista', 'Prazo Limite', 'Horas Atraso',
                'Entregue', 'Entregue Por',
            ];

            foreach ($this->delays['delayed'] as $row) {
=======
        $rowNum = 1;

        // == Summary block ======
        $this->sectionHeaderRows[] = $rowNum;
        $rows[] = ['RESUMO DE ATRASOS', '', '', '', '', '', '', '', '', ''];
        $rowNum++;

        $this->tableHeaderRows[] = $rowNum;
        $rows[] = ['Indicador', 'Total', '', '', '', '', '', '', '', ''];
        $rowNum++;

        $summaryData = [
            ['Total Encomendas Analisadas', $this->delays['summary']['total_analysed']],
            ['Em Atraso', $this->delays['summary']['total_delayed']],
            ['Dentro do Prazo', $this->delays['summary']['total_on_time']],
        ];

        foreach ($summaryData as $i => [$label, $value]) {
            $rows[] = [$label, $value, '', '', '', '', '', '', '', ''];
            $rowNum++;
        }

        $rows[] = ['', '', '', '', '', '', '', '', '', ''];
        $rowNum++;

        // == Delayed orders ===========
        $this->sectionHeaderRows[] = $rowNum;
        $rows[] = ['ENCOMENDAS EM ATRASO (' . $this->delays['summary']['total_delayed'] . ')', '', '', '', '', '', '', '', '', ''];
        $rowNum++;

        if (empty($this->delays['delayed'])) {
            $rows[] = ['Nenhuma encomenda em atraso encontrada.', '', '', '', '', '', '', '', '', ''];
            $rowNum++;
        } else {
            $this->tableHeaderRows[] = $rowNum;
            $rows[] = ['Tracking', 'Cliente', 'Origem', 'Destino', 'Serviço', 'Saída Prevista', 'Prazo Limite', 'Horas Atraso', 'Entregue', 'Entregue Por'];
            $rowNum++;

            foreach ($this->delays['delayed'] as $row) {
                $this->delayedDataRows[] = $rowNum;
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
                $rows[] = [
                    $row['tracking'],
                    $row['client'],
                    $row['origin'],
                    $row['destination'],
                    $row['service_type'],
<<<<<<< HEAD
                    $row['analysis']['actual_departure_at']
                        ? \Carbon\Carbon::parse($row['analysis']['actual_departure_at'])->format('d/m/Y H:i')
                        : '—',
                    $row['analysis']['deadline_at']
                        ? \Carbon\Carbon::parse($row['analysis']['deadline_at'])->format('d/m/Y H:i')
                        : '—',
                    '+' . $row['analysis']['delay_hours'] . 'h',
                    $row['analysis']['is_delivered'] ? 'Sim' : 'Não',
                    $row['analysis']['delivered_by'] ?? '—',
                ];
            }

            $rows[] = ['', '', '', '', '', '', '', '', '', ''];
        }

        if (! empty($this->delays['on_time'])) {
            $rows[] = ['ENCOMENDAS DENTRO DO PRAZO', '', '', '', '', '', '', '', '', ''];
            $rows[] = [
                'Tracking', 'Cliente', 'Origem', 'Destino', 'Serviço',
                'Saída Prevista', 'Prazo Limite', 'Horas Decorridas',
                'Entregue', 'Entregue Por',
            ];

            foreach ($this->delays['on_time'] as $row) {
=======
                    $row['analysis']['actual_departure_at'] ? Carbon::parse($row['analysis']['actual_departure_at'])->format('d/m/Y H:i') : '-',
                    $row['analysis']['deadline_at']         ? Carbon::parse($row['analysis']['deadline_at'])->format('d/m/Y H:i')         : '-',
                    '+' . $row['analysis']['delay_hours'] . 'h',
                    $row['analysis']['is_delivered'] ? 'Sim' : 'Não',
                    $row['analysis']['delivered_by'] ?? '-',
                ];
                $rowNum++;
            }
        }

        $rows[] = ['', '', '', '', '', '', '', '', '', ''];
        $rowNum++;

        // == On time orders =======
        $this->sectionHeaderRows[] = $rowNum;
        $rows[] = ['ENCOMENDAS DENTRO DO PRAZO (' . $this->delays['summary']['total_on_time'] . ')', '', '', '', '', '', '', '', '', ''];
        $rowNum++;

        if (empty($this->delays['on_time'])) {
            $rows[] = ['Nenhuma encomenda dentro do prazo encontrada.', '', '', '', '', '', '', '', '', ''];
            $rowNum++;
        } else {
            $this->tableHeaderRows[] = $rowNum;
            $rows[] = ['Tracking', 'Cliente', 'Origem', 'Destino', 'Serviço', 'Saída Prevista', 'Prazo Limite', 'Horas Decorridas', 'Entregue', 'Entregue Por'];
            $rowNum++;

            foreach ($this->delays['on_time'] as $row) {
                $this->onTimeDataRows[] = $rowNum;
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
                $rows[] = [
                    $row['tracking'],
                    $row['client'],
                    $row['origin'],
                    $row['destination'],
                    $row['service_type'],
<<<<<<< HEAD
                    $row['analysis']['actual_departure_at']
                        ? \Carbon\Carbon::parse($row['analysis']['actual_departure_at'])->format('d/m/Y H:i')
                        : '—',
                    $row['analysis']['deadline_at']
                        ? \Carbon\Carbon::parse($row['analysis']['deadline_at'])->format('d/m/Y H:i')
                        : '—',
                    $row['analysis']['elapsed_hours'] . 'h',
                    $row['analysis']['is_delivered'] ? 'Sim' : 'Não',
                    $row['analysis']['delivered_by'] ?? '—',
                ];
            }

            $rows[] = ['', '', '', '', '', '', '', '', '', ''];
        }

        if (! empty($this->delays['no_config'])) {
            $rows[] = ['SEM CONFIGURAÇÃO DE ROTA', '', '', '', '', '', '', '', '', ''];
            $rows[] = ['Tracking', 'Cliente', 'Origem', 'Destino', 'Serviço', '', '', '', '', ''];

            foreach ($this->delays['no_config'] as $row) {
                $rows[] = [
                    $row['tracking'],
                    $row['client'],
                    $row['origin'],
                    $row['destination'],
                    $row['service_type'],
                    '', '', '', '', '',
                ];
            }
        }

=======
                    $row['analysis']['actual_departure_at'] ? Carbon::parse($row['analysis']['actual_departure_at'])->format('d/m/Y H:i') : '-',
                    $row['analysis']['deadline_at']         ? Carbon::parse($row['analysis']['deadline_at'])->format('d/m/Y H:i')         : '-',
                    $row['analysis']['elapsed_hours'] . 'h',
                    $row['analysis']['is_delivered'] ? 'Sim' : 'Não',
                    $row['analysis']['delivered_by'] ?? '-',
                ];
                $rowNum++;
            }
        }


        $this->accentRow = $rowNum;
        $rows[] = ['', '', '', '', '', '', '', '', '', ''];

>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
        return $rows;
    }

    public function title(): string
    {
        return 'Atrasos';
    }

<<<<<<< HEAD
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
=======
    public function columnWidths(): array
    {
        return ['A' => 32, 'B' => 24, 'C' => 16, 'D' => 18, 'E' => 14, 'F' => 18, 'G' => 18, 'H' => 16, 'I' => 10, 'J' => 22];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Section headers - purple full width
                foreach ($this->sectionHeaderRows as $row) {
                    $sheet->mergeCells("A{$row}:J{$row}");
                    $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => self::WHITE]],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::PURPLE]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(22);
                }

                // Table headers - lime
                foreach ($this->tableHeaderRows as $row) {
                    $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => '3E2723'], 'size' => 9],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::LIME]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BBBBBB']]],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(20);
                    $sheet->freezePane("A" . ($row + 1));
                }

                // Delayed rows - red tint
                foreach ($this->delayedDataRows as $row) {
                    $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::RED_BG]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFCDD2']]],
                    ]);
                    // Column H (Horas Atraso) in bold red
                    $sheet->getStyle("H{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => self::RED_FG]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(18);
                }

                // On time rows - green tint
                foreach ($this->onTimeDataRows as $row) {
                    $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::GRN_BG]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C8E6C9']]],
                    ]);
                    $sheet->getStyle("H{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => self::GRN_FG]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(18);
                }
            },
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
        ];
    }
}