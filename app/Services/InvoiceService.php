<?php

namespace App\Services;

use App\Models\Invoice;

class InvoiceService 
{
    public function createInvoice(array $data)
    {
        return Invoice::create($data);
    }

    public function getAllInvoice()
    {
        return Invoice::with('orders')->get();
    }

    public function getInvoiceById(string $id)
    {
        return Invoice::findOrFail($id);
    }

    public function updateInvoice(string $id, array $data)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update($data);

        return $invoice;
    }

    public function deleteInvoice(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        return $invoice->delete();
    }
}