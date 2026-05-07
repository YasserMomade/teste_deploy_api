<?php

namespace App\Observers;

use App\Models\Store;
use App\Services\AuditLogService;

class OrderObserver
{
    public function created(Store $store): void
    {
        AuditLogService::log('created', $store, [], $store->getAttributes());
    }

    public function updated(Store $store): void
    {
        AuditLogService::log('updated', $store, $store->getOriginal(), $store->getChanges());
    }

    public function deleted(Store $store): void
    {
        AuditLogService::log('deleted', $store, $store->getAttributes(), []);
    }
}
