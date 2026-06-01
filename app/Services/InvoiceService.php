<?php

namespace App\Services;

use App\Models\Invoice;
use Stripe\Price;
use Stripe\PaymentLink;
use Stripe\Stripe;
use Stripe\Webhook;

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
    public function generatePaymentLink(string $invoiceId): Invoice
    {
        $invoice = Invoice::findOrFail($invoiceId);

        Stripe::setApiKey(config('services.stripe.secret'));

        $amountInCents = (int) round($invoice->amountTo_pay * 100);

        $price = Price::create([
            'currency' => 'eur',
            'unit_amount' => $amountInCents,
            'product_data' => [
                'name' => 'Fatura #' . ($invoice->referencie ?? $invoiceId),
            ],
        ]);

        $paymentLink = PaymentLink::create([
            'line_items' => [[
                'price' => $price->id,
                'quantity' => 1,
            ]],
        ]);

        $invoice->update([
            'stripe_payment_link' => $paymentLink->url,
            'stripe_price_id' => $price->id,
        ]);

        return $invoice->fresh();
    }

    public function handleStripeWebhook(string $payload, string $sigHeader): void
    {
        $event = Webhook::constructEvent($payload, $sigHeader, config('services.stripe.webhook_secret'));

        switch ($event->type) {

            case 'checkout.session.completed':
                $session = $event->data->object;

                Stripe::setApiKey(config('services.stripe.secret'));

                try {

                    $session = \Stripe\Checkout\Session::retrieve([
                        'id'     => $session->id,
                        'expand' => ['line_items'],
                    ]);
                    $this->markInvoiceAsPaid($session);
                } catch (\Exception $e) {

                    \Log::warning('Stripe: não foi possível expandir sessão, tentando pelo payment_link: ' . $e->getMessage());
                    $this->markInvoiceAsPaidByPaymentLink($session->payment_link ?? null);
                }
                break;

            case 'payment_intent.payment_failed':
                \Log::warning('Stripe: pagamento falhou - ' . $event->data->object->id);
                break;
        }
    }

    private function markInvoiceAsPaid(object $session): void
    {
        $priceId = $session->line_items->data[0]->price->id ?? null;

        if (!$priceId) {
            \Log::error('Stripe webhook: price_id não encontrado na sessão ' . $session->id);
            return;
        }

        $invoice = Invoice::where('stripe_price_id', $priceId)->first();

        if (!$invoice) {
            \Log::error('Stripe webhook: fatura não encontrada para price_id ' . $priceId);
            return;
        }

        $invoice->update([
            'payment_status' => 'paid',
            'amount_paid'    => $invoice->amountTo_pay,
        ]);

        \Log::info('Stripe: fatura #' . $invoice->id . ' marcada como paga.');
    }

    private function markInvoiceAsPaidByPaymentLink(?string $paymentLinkId): void
    {
        if (!$paymentLinkId) {
            \Log::error('Stripe webhook: payment_link_id em falta.');
            return;
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $link = \Stripe\PaymentLink::retrieve($paymentLinkId);

        $invoice = Invoice::where('stripe_payment_link', $link->url)->first();

        if (!$invoice) {
            \Log::error('Stripe webhook: fatura não encontrada para payment_link ' . $paymentLinkId);
            return;
        }

        $invoice->update([
            'payment_status' => 'paid',
            'amount_paid'    => $invoice->amountTo_pay,
        ]);

        \Log::info('Stripe: fatura #' . $invoice->id . ' marcada como paga via payment_link.');
    }
}
