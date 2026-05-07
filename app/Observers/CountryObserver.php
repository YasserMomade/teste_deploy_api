<?php

namespace App\Observers;

use App\Models\Country;
use App\Services\AuditLogService;

class CountryObserver
{
    public function created(Country $country): void
    {
        AuditLogService::log('created', $country, [], $country->getAttributes());
    }

    public function updated(Country $country): void
    {
        AuditLogService::log('updated', $country, $country->getOriginal(), $country->getChanges());
    }

    public function deleted(Country $country): void
    {
        AuditLogService::log('deleted',$country,$country->getAttributes(), []);
    }
}