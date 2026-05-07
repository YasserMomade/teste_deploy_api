<?php

namespace App\Observers;


use App\Models\User;
use App\Services\AuditLogService;

class UserObserver
{
    public function created(User $user): void
    {
        AuditLogService::log('created', $user, [], $user->getAttributes());
    }

    public function updated(User $user): void
    {
        AuditLogService::log('updated', $user, $user->getOriginal(), $user->getChanges());
    }

    public function deleted(User $user) : void 
    {
        AuditLogService::log('deleted', $user, $user->getAttributes(), []);
    }
}