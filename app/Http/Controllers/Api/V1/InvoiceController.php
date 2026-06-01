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
            $this->invoiceService->deleteInvoice($id);
            return $this->success(null, 'Invoice deleted successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function generatePaymentLink(string $id): JsonResponse
    {

        try {
            $existing = $this->invoiceService->getInvoiceById($id);

            if ($existing->stripe_payment_link) {
                return $this->success($existing, 'Link de pagamento já existente.');
            }

            $invoice = $this->invoiceService->generatePaymentLink($id);

            return $this->success($invoice, 'Link de pagamento gerado com sucesso.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function handleWebhook(Request $request): JsonResponse
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        \Log::info('Webhook debug', [
            'sig_header' => $sigHeader,
            'secret'     => config('services.stripe.webhook_secret'),
            'payload_length' => strlen($payload),
        ]);

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
