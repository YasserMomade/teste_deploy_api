<?php 

namespace App\Observers;

use App\Models\Counter;
use App\Services\AuditLogService;

class CounterObserver
{
    public function created(Counter $counter): void
    {
        AuditLogService::log('created', $counter, [], $counter->getAttributes());
    }

    public function updated(Counter $counter): void
    {
        AuditLogService::log('updated', $counter, $counter->getOriginal(), $counter->getChanges());
    }

    public function deleted(Counter $counter): void
    {
        AuditLogService::log('deleted', $counter, $counter->getAttributes(), []);
    }
}