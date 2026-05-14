<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExceptionReportExport implements WithMultipleSheets
{
    public function __construct(private readonly array $data) {}

    public function sheets(): array
    {
        return [
            new Sheets\ExceptionSummarySheet($this->data['summary']),
            new Sheets\ExceptionDelaysSheet($this->data['delays']),
            new Sheets\ExceptionQualitySheet($this->data['quality']),
             new Sheets\ExceptionWithoutClientSheet($this->data['orders_without_client']),
        ];
    }
}