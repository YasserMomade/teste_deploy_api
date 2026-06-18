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
            string $tracking,
            string $trackingToken,
            string $clientName
        ): array {

            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $phone,
                'type' => 'template',
                'template' => [
                    'name' => 'tracking'    ,
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
                                    'text' => $trackingToken
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
                                    'text' => $tracking
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

        $clean_link = ltrim(str_replace('https://pay.portadordiario.co', '', $payment_link), '/');

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => [
                'name' => 'order_pick_up',
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
                'name' => 'payment_confirmed',
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

    public function sendDataSaved(
        string $phone,
        string $name
    ) {
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => [
                'name' => 'order_request_saved',
                'language' => [
                    'code' => 'en' 
                ],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $name
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
