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
            new Sheets\ExceptionWithoutClientSheet($this->data['orders_without_client']),
            new Sheets\ExceptionWithoutInvoiceSheet($this->data['orders_without_invoice']),
            new Sheets\ExceptionWithoutDeclaredWeightSheet($this->data['orders_without_declared_weight']),
            new Sheets\ExceptionWithoutStatusSheet($this->data['orders_without_status']),
            new Sheets\ExceptionInvoicesNullStatusSheet($this->data['invoices_with_null_status']),
            new Sheets\ExceptionDelaysSheet($this->data['delays']),
            new Sheets\ExceptionQualitySheet($this->data['quality']),
        ];
    }
}