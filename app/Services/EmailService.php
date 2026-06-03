<?php

namespace App\Services;

use Brevo\Brevo;
use Brevo\TransactionalEmails\Requests\SendTransacEmailRequest;
use Brevo\TransactionalEmails\Types\SendTransacEmailRequestSender;
use Brevo\TransactionalEmails\Types\SendTransacEmailRequestToItem;

class EmailService
{
    private Brevo $client;

    public function __construct()
    {
        $this->client = new Brevo(config('services.brevo.api_key'));
    }

    public function sendWelcomeEmail(string $toEmail, string $toName, string $password): void
    {
        $appUrl = config('app.frontend_url', 'https://teste-deploy-taupe.vercel.app');

        $sender = new SendTransacEmailRequestSender([
            'email' => config('mail.from.address'),
            'name' => config('mail.from.name'),
        ]);

        $request = new SendTransacEmailRequest([
            'sender' => $sender,
            'to' => [new SendTransacEmailRequestToItem(['email' => $toEmail, 'name' => $toName])],
            'subject' => 'Bem-vindo ao Portador Diário Sistema DROOP OFF - As suas credenciais de acesso',
            'htmlContent' => "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: #9B267C; padding: 30px; text-align: center;'>
                        <h1 style='color: white; margin: 0;'>Portador Diário</h1>
                    </div>
                    <div style='padding: 30px; background: #f9f9f9;'>
                        <h2 style='color: #333;'>Olá, {$toName}!</h2>
                        <p style='color: #555;'>A sua conta foi criada com sucesso. Aqui estão as suas credenciais de acesso:</p>
                        <div style='background: white; border-left: 4px solid #9B267C; padding: 20px; margin: 20px 0;'>
                            <p style='margin: 5px 0;'><strong>Email:</strong> {$toEmail}</p>
                            <p style='margin: 5px 0;'><strong>Password:</strong> {$password}</p>
                        </div>
                        <p style='color: #555; margin-top: 20px;'>Aceda à aplicação através do link abaixo:</p>
                        <div style='text-align: center; margin: 20px 0;'>
                            <a href='{$appUrl}' style='background: #9B267C; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block;'>
                                Aceder à aplicação
                            </a>
                        </div>
                        <p style='color: #e74c3c; font-size: 13px;'>Por razões de segurança, recomendamos que altere a sua password após o primeiro login.</p>
                    </div>
                    <div style='background: #333; padding: 15px; text-align: center;'>
                        <p style='color: #aaa; font-size: 12px; margin: 0;'>© " . date('Y') . " Portador Diário. Todos os direitos reservados.</p>
                    </div>
                </div>
            ",
        ]);

        $this->client->transactionalEmails->sendTransacEmail($request);
    }
}