<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Store;

class StoreObserver
{
    public function created(Store $store): void
    {
        AuditLog::log('created', $store, [], $store->getAttributes());
    }

    public function updated(Store $store): void
    {
        AuditLog::log('updated', $store, $store->getOriginal(), $store->getChanges());
    }

    public function deleted(Store $store) : void 
    {
        AuditLog::log('deleted', $store, $store->getAttributes(), []);
    }
}