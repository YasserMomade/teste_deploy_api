<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\InvoiceService;
use App\Http\Requests\Invoice\StoreInvoice;


class InvoiceController extends Controller
{
    use ApiResponse;

    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }  
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $invoice = $this->invoiceService->getAllInvoice();
            return $this->success($invoice);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoice $request)
    {
        try {
            $invoice = $this->invoiceService->createInvoice($request->validated());
            return $this->created($invoice);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(String $id)
    {
        try {
            $invoice = $this->invoiceService->getInvoiceById($id);
            return $this->success($invoice);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(StoreInvoice $request, string $id)
    {
        try {
            $invoice = $this->invoiceService->updateInvoice($id, $request->validated());
            return $this->success($invoice);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->invoiceService->deletePrice($id);
            return $this->success(null, 'Invoice deleted successfully');

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}

