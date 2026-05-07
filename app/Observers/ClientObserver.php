<?php 

namespace App\Observers;

use App\Models\Client;
use App\Services\AuditLogService;

class ClientObserver
{
    public function created(Client $client): void
    {
        AuditLogService::log('created', $client, [], $client->getAttributes());
    }

    public function updated(Client $client): void
    {
        AuditLogService::log('updated', $client, $client->getOriginal(), $client->getChanges());
    }

    public function deleted(Client $client): void
    {
        AuditLogService::log('deleted', $client, $client->getAttributes(), []);
    }
}