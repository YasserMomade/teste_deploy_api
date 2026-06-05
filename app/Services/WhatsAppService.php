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
            string $imageUrl,
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
                                        'link' => $imageUrl
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

    public function sendPaymentLink(
        string $phone,
        string $imageUrl,
        string $clientName,
        string $payment_link
    ) {

        $clean_link = ltrim(str_replace('https://buy.stripe.com', '', $payment_link), '/');

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => [
                'name' => 'order_ready_pickup_3',
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
                                    'link' => $imageUrl
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
                            ]
                        ]
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'url',
                        'index' => '0',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $clean_link
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

        Log::info('WhatsApp Payment Link Template', [
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

    public function paymentConfirm(
        string $phone,
        string $clientName,
        string $pick_up_code
    ) {
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => [
                'name' => 'payment_confirmed_4',
                'language' => [
                    'code' => 'en' 
                ],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $clientName
                            ],
                            [
                                'type' => 'text',
                                'text' => $pick_up_code
                            ]
                        ]
                    ]                                    
                ],
            ]
        ];

        $response = Http::withToken($this->token)
            ->timeout(30)
            ->connectTimeout(20)
            ->post(
                "https://graph.facebook.com/v25.0/{$this->phoneNumberId}/messages",
                $payload
            );

        Log::error('WhatsApp API Error', [
            'status'   => $response->status(),
            'body'     => $response->json(),
            'phone_id' => $this->phoneNumberId,
        ]);

        if (!$response->successful()) {
            throw new \Exception(
                $response->json()['error']['message']
                . ' | Code: ' . ($response->json()['error']['code'] ?? 'N/A')
                . ' | Error subcode: ' . ($response->json()['error']['error_subcode'] ?? 'N/A')
            );
        }

        return $response->json();
    }
}