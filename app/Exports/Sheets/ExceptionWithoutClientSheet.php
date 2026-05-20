<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
<<<<<<< HEAD
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExceptionWithoutClientSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, ShouldAutoSize
{
=======
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExceptionWithoutClientSheet implements FromCollection, WithTitle, WithHeadings, WithColumnWidths, WithEvents
{
    private const PURPLE = '962479';
    private const LIME = 'C5D22D';
    private const WHITE = 'FFFFFF';
    private const LIGHT = 'F9F0F6';

>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
    public function __construct(private readonly mixed $orders) {}

    public function collection()
    {
        return collect($this->orders)->map(fn($order) => [
            $order->tracking,
            $order->destination,
<<<<<<< HEAD
            $order->reception_date?->format('d/m/Y'),
            $order->weight,
            $order->store?->name ?? '—',
            $order->responsible?->name ?? '—',
=======
            $order->reception_date?->format('d/m/Y') ?? '-',
            $order->weight,
            $order->store?->name ?? '-',
            $order->responsible?->full_name ?? '-',
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
        ]);
    }

    public function headings(): array
    {
        return ['Tracking', 'Destino', 'Data Recepção', 'Peso (kg)', 'Loja', 'Responsável'];
    }

    public function title(): string
    {
        return 'Sem Cliente';
    }

<<<<<<< HEAD
    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
=======
    public function columnWidths(): array
    {
        return ['A' => 22, 'B' => 20, 'C' => 16, 'D' => 12, 'E' => 20, 'F' => 22];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $this->orders instanceof \Illuminate\Support\Collection
                    ? $this->orders->count() + 1
                    : count($this->orders) + 1;
                $lastCol = 'F';
                $range = "A1:{$lastCol}1";
                $dataRange = "A2:{$lastCol}{$lastRow}";

                // Header row
                $sheet->getStyle($range)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => self::WHITE], 'size' => 10],
                    'fill'=> ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::PURPLE]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BBBBBB']]],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(22);

                // Alternating data rows
                for ($row = 2; $row <= $lastRow; $row++) {
                    $bg = ($row % 2 === 0) ? self::LIGHT : self::WHITE;
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'EEEEEE']]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(18);
                }

                // Outer border around entire table
                $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::PURPLE]],
                    ],
                ]);

                // Freeze top row
                $sheet->freezePane('A2');

            },
        ];
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
    }
}