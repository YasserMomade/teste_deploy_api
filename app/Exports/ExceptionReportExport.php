<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets;

class ExceptionReportExport implements WithMultipleSheets
{
    public function __construct(private array $data) {}

    public function sheets(): array
    {
        return [
            new Sheets\ExceptionSummarySheet($this->data['summary']),
            new Sheets\ExceptionDelaysSheet($this->data['delays']),
            new Sheets\ExceptionStalledSheet($this->data['stalled']),
            new Sheets\ExceptionQualitySheet($this->data['quality']),
        ];
    }
}