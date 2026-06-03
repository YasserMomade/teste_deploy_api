<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $token;
    protected string $phoneNumberId;

    public function __construct()
    {
        $this->token = config('whatsapp.token');
        $this->phoneNumberId = config('whatsapp.phone_number_id');
    }

    public function sendTrackingMessage(
        string $phone,
        // string $imageUrl,
        string $trackingLink,
        string $trackingToken,
        string $clientName
    ): array {

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => [
                'name' => 'rastreamento', 
                'language' => [
                    'code' => 'en'
                ],
                'components' => [
                    [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => 'image',
                                'image' => [
                                    'link' => 'https://yt3.googleusercontent.com/PvD4aN5Hip4POyZGogzy2WWHTvEB-RBbZFl36wvOdb6F8TU39BbqRqW0L4HXRa7T-HWvr0ov9Q=s900-c-k-c0x00ffffff-no-rj'
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $clientName
                            ],
                            [
                                'type' => 'text',
                                'text' => $trackingLink
                            ],
                            [
                                'type' => 'text',
                                'text' => $trackingToken
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = Http::withToken($this->token)
            ->timeout(30)
            ->connectTimeout(20)
            ->post(
                "https://graph.facebook.com/v25.0/{$this->phoneNumberId}/messages",
                $payload
            );

        Log::info('WhatsApp Tracking Template', [
            'payload' => $payload,
            'response' => $response->json(),
        ]);

        if (!$response->successful()) {
            throw new \Exception(
                $response->json()['error']['message']
                ?? 'Erro ao enviar template WhatsApp'
            );
        }

        return $response->json();
    }
}