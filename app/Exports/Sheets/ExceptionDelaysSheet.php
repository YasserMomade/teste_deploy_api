<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExceptionDelaysSheet implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
    public function __construct(private readonly array $delays) {}

    public function array(): array
    {
        $rows = [];

      
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
                $rows[] = [
                    $row['tracking'],
                    $row['client'],
                    $row['origin'],
                    $row['destination'],
                    $row['service_type'],
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
                $rows[] = [
                    $row['tracking'],
                    $row['client'],
                    $row['origin'],
                    $row['destination'],
                    $row['service_type'],
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

        return $rows;
    }

    public function title(): string
    {
        return 'Atrasos';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}