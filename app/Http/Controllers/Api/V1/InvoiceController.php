<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\InvoiceService;
use App\Http\Requests\Invoice\StoreInvoice;

class InvoiceController extends Controller
{
    use ApiResponse;

    public function __construct(
        private InvoiceService $invoiceService
    ) {}

    public function index()
    {
        try {
            return $this->success($this->invoiceService->getAllInvoice());
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function store(StoreInvoice $request)
    {
        try {
            $invoice = $this->invoiceService->createInvoice($request->validated());
            return $this->created($invoice);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function show(string $id)
    {
        try {
            return $this->success($this->invoiceService->getInvoiceById($id));
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update(StoreInvoice $request, string $id)
    {
        try {
            $invoice = $this->invoiceService->updateInvoice($id, $request->validated());
            return $this->success($invoice);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->invoiceService->deleteInvoice($id);
            return $this->success(null, 'Fatura eliminada com sucesso.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function generatePaymentLink(string $id): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->getInvoiceById($id);

            if ($invoice->stripe_payment_link) {
                return $this->success($invoice, 'Link de pagamento já existente.');
            }

            $invoice = $this->invoiceService->generatePaymentLink($id);
            return $this->success($invoice, 'Link de pagamento gerado com sucesso.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        if (!$sigHeader) {
            return $this->error('Assinatura Stripe em falta.', 400);
        }

        try {
            $this->invoiceService->handleStripeWebhook($payload, $sigHeader);
            return $this->success(null, 'Webhook processado com sucesso.');
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            \Log::error('Stripe assinatura inválida: ' . $e->getMessage());
            return $this->error('Assinatura Stripe inválida.', 400);
        } catch (\Exception $e) {
            \Log::error('Stripe webhook erro: ' . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }

}