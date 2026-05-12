<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\AuditLogService;

class OrderObserver
{
    public function created(Order $order): void
    {
        AuditLogService::log('created', $order, [], $order->getAttributes());
    }

    public function updated(Order $order): void
    {
        AuditLogService::log('updated', $order, $order->getOriginal(), $order->getChanges());
    }

    public function deleted(Order $order): void
    {
        AuditLogService::log('deleted', $order, $order->getAttributes(), []);
    }
}
