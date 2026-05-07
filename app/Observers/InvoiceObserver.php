<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Services\AuditLogService;

class InvoiceObserver
{
    public function created(Invoice $invoice): void
    {
        AuditLogService::log('created', $invoice, [], $invoice->getAttributes());
    }

    public function updated(Invoice $invoice): void
    {
        AuditLogService::log('updated', $invoice, $invoice->getOriginal(), $invoice->getChanges());
    }

    public function deleted(Invoice $invoice): void
    {
        AuditLogService::log('deleted', $invoice, $invoice->getAttributes(), []);
    }
}
