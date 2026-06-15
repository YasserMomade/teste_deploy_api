<?php

namespace App\Services;

use App\Models\Invoice;
use Stripe\Price;
use Stripe\PaymentLink;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceService
{
    public function __construct(
        private WhatsAppService $whatsAppService
    ) {}

    public function createInvoice(array $data): Invoice
    {
        return Invoice::create($data);
    }

    public function getAllInvoice()
    {
        return Invoice::with('orders.client')->get();
    }

    public function getInvoiceById(string $id): Invoice
    {
        return Invoice::with('orders.client')->findOrFail($id);
    }

    public function updateInvoice(string $id, array $data): Invoice
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update($data);
        return $invoice;
    }

    public function deleteInvoice(string $id): bool
    {
        $invoice = Invoice::findOrFail($id);
        return $invoice->delete();
    }

    public function generatePdf(string $id): string
    {
        $invoice = Invoice::with('orders.client')->findOrFail($id);

        return Pdf::loadView('pdf.invoice', compact('invoice'))
            ->setPaper('a4', 'portrait')
            ->output();
    }

    public function generatePaymentLink(string $id): Invoice
    {

        $invoice = Invoice::findOrFail($id);

        Stripe::setApiKey(config('services.stripe.secret'));

        $amountInCents = (int) round($invoice->amountTo_pay * 100);

        $price = Price::create([
            'currency'     => 'eur',
            'unit_amount'  => $amountInCents,
            'product_data' => [
                'name' => 'Fatura #' . $invoice->referencie,
            ],
        ]);

        $paymentLink = PaymentLink::create([
            'line_items' => [['price' => $price->id, 'quantity' => 1]],
            'payment_method_types' => ['card'],
            'customer_creation' => 'always',
        ]);

        $invoice->update([
            'stripe_payment_link' => $paymentLink->url,
            'stripe_price_id' => $price->id,
        ]);

        return $invoice->fresh();
    }

    public function handleStripeWebhook(string $payload, string $sigHeader): void
    {
        $event = Webhook::constructEvent(
            $payload,
            $sigHeader,
            config('services.stripe.webhook_secret')
        );

        switch ($event->type) {

            case 'checkout.session.completed':

                Stripe::setApiKey(config('services.stripe.secret'));

                $session = $event->data->object;

                try {

                    $session = \Stripe\Checkout\Session::retrieve([
                        'id' => $session->id,
                        'expand' => ['line_items'],
                    ]);

                    $paymentIntent = \Stripe\PaymentIntent::retrieve(
                        $session->payment_intent
                    );

                    $charge = \Stripe\Charge::retrieve(
                        $paymentIntent->latest_charge
                    );

                    \Log::info('Receipt URL: ' . ($charge->receipt_url ?? 'Sem Recibooo'));

                    $this->markInvoiceAsPaid(
                        $session,
                        $paymentIntent,
                        $charge
                    );
                } catch (\Exception $e) {

                    \Log::error(
                        'Stripe checkout.session.completed erro: ' .
                            $e->getMessage()
                    );

                    throw $e;
                }

                break;

            case 'payment_intent.payment_failed':

                \Log::warning(
                    'Stripe: pagamento falhou - ' .
                        $event->data->object->id
                );

                break;
        }
    }
    private function markInvoiceAsPaid(
        object $session,
        object $paymentIntent,
        object $charge
    ): void {
        $priceId = $session->line_items->data[0]->price->id ?? null;

        if (!$priceId) {

            \Log::error(
                'Stripe webhook: price_id não encontrado na sessão ' .
                    $session->id
            );

            return;
        }

        $invoice = Invoice::with('orders.client')
            ->where('stripe_price_id', $priceId)
            ->first();

        if (!$invoice) {

            \Log::error(
                'Stripe webhook: fatura não encontrada para price_id ' . $priceId
            );

            return;
        }

        $invoice->update([
            'stripe_payment_intent' => $paymentIntent->id,
            'stripe_receipt_url'    => $charge->receipt_url ?? null,
        ]);

        $this->processPaymentConfirmation($invoice->fresh());
    }


    private function processPaymentConfirmation(Invoice $invoice): void
    {
        if ($invoice->payment_status === 'paid') {

            \Log::info(
                'Stripe: fatura #' .
                    $invoice->id .
                    ' já estava paga, ignorando.'
            );

            return;
        }

        $invoice->update([
            'payment_status' => 'paid',
            'amount_paid' => $invoice->amountTo_pay,
            'payment_method' => 'card',
            'paid_at' => now(),
        ]);

        $order  = $invoice->orders->first();
        $client = $order?->client;

        if ($client && $order) {

            $receiptUrl = $invoice->stripe_receipt_url;

            $this->whatsAppService->paymentConfirm(
                $client->phone,
                $client->name,
                $order->pick_up_code,
                $receiptUrl
            );
        }

        \Log::info(
            'Stripe: fatura #' .
                $invoice->id .
                ' (ref: ' . $invoice->referencie . ') marcada como paga em ' . now()->toDateTimeString()
        );
    }
}
