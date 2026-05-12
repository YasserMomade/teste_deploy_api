<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExceptionQualitySheet implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
    public function __construct(private readonly array $quality) {}

    public function array(): array
    {
        $rows = [];

      
        $rows[] = ['Índice de Qualidade Operacional', ''];
        $rows[] = ['Score', $this->quality['score']['score'] . ' / 4.00'];
        $rows[] = ['Percentagem', $this->quality['score']['percentage'] . '%'];
        $rows[] = ['Classificação', $this->quality['score']['label']];
        $rows[] = ['', ''];

    
        $rows[] = ['Resumo de Observações', ''];
        $rows[] = ['Total', $this->quality['summary']['total_observations']];
        $rows[] = ['Good', $this->quality['summary']['total_good']];
        $rows[] = ['Medium', $this->quality['summary']['total_medium']];
        $rows[] = ['Bad', $this->quality['summary']['total_bad']];
        $rows[] = ['Critical', $this->quality['summary']['total_critical']];
        $rows[] = ['', ''];

     
        if (! empty($this->quality['by_responsible'])) {
            $rows[] = ['Qualidade por Colaborador', '', '', '', '', '', '', ''];
            $rows[] = ['Colaborador', 'Código', 'Total', 'Good', 'Medium', 'Bad', 'Critical', 'Score', 'Classificação'];

            foreach ($this->quality['by_responsible'] as $row) {
                $rows[] = [
                    $row['responsible'],
                    $row['user_code'],
                    $row['total'],
                    $row['good'],
                    $row['medium'],
                    $row['bad'],
                    $row['critical'],
                    $row['score'] . '/4',
                    $row['score_label'],
                ];
            }

            $rows[] = ['', ''];
        }

        
        if (! empty($this->quality['critical_and_bad_orders'])) {
            $rows[] = ['Encomendas com Observações Críticas ou Más', '', '', '', ''];
            $rows[] = ['Tracking', 'Cliente', 'Destino', 'Serviço', 'Responsável', 'Nível', 'Descrição', 'Registado por', 'Data'];

            foreach ($this->quality['critical_and_bad_orders'] as $order) {
                foreach ($order['observations'] as $obs) {
                    $rows[] = [
                        $order['tracking'],
                        $order['client'],
                        $order['destination'],
                        $order['service_type'],
                        $order['responsible'],
                        ucfirst($obs['level']),
                        $obs['description'],
                        $obs['created_by'],
                        $obs['created_at'],
                    ];
                }
            }

            $rows[] = ['', ''];
        }

      
        if (! empty($this->quality['trend'])) {
            $rows[] = ['Tendência de Qualidade por Dia', '', '', '', ''];
            $rows[] = ['Data', 'Good', 'Medium', 'Bad', 'Critical'];

            foreach ($this->quality['trend'] as $row) {
                $rows[] = [
                    \Carbon\Carbon::parse($row['date'])->format('d/m/Y'),
                    $row['good'],
                    $row['medium'],
                    $row['bad'],
                    $row['critical'],
                ];
            }
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Qualidade';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}