<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        AuditLog::log('created', $user, [], $user->getAttributes());
    }

    public function updated(User $user): void
    {
        AuditLog::log('updated', $user, $user->getOriginal(), $user->getChanges());
    }

    public function deleted(User $user) : void 
    {
        AuditLog::log('deleted', $user, $user->getAttributes(), []);
    }
}