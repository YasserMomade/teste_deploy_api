<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use App\Models\Order; 
use Illuminate\Http\JsonResponse;

class WhatsAppController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected WhatsAppService $whatsappService
    ) {}

    public function orderRecivied(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::with('client')->findOrFail($request->order_id);

        $phone        = '+258' . ltrim($order->client->phone, '0');
        $name         = $order->client->name;
        $trackingCode = $order->tracking_code;
        $trackingUrl  = 'http://localhost:5173/landingPage';
        $imageUrl     = asset('images/encomenda.jpg');

        $this->whatsappService->sendPaymentLink( 
            phone: $phone,
            name: $name,
            trackingCode: $trackingCode,
            trackingUrl: $trackingUrl,
            imageUrl: $imageUrl,
        );

        return response()->json(['ok' => true]);
    }

    public function paymentDone(): JsonResponse
    {
        $phone = '258843799864';
        $clientName = 'Ian';
        $pick_up_code = 'fdfdfdfdfd';

        $this->whatsappService->paymentConfirm( 
            phone: $phone,
            clientName: $clientName,
            pick_up_code: $pick_up_code
        );

        return response()->json(['ok' => true]);
    }
}