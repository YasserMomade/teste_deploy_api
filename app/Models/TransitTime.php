<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransitTime extends Model
{
      use HasFactory;

 protected $fillable = [
        'origin_country_id',
        'destination_country_id',
        'service_type',
        'expected_hours',
        'departure_days',
    ];

      protected $casts = [
        'departure_days' => 'array',
        'expected_hours' => 'integer',
    ];

       public function originCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'origin_country_id');
    }

     public function destinationCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'destination_country_id');
    }

     public function getNextDeparture(\Carbon\Carbon $from): \Carbon\Carbon
    {
        $days = $this->departure_days; // e.x. [1, 5] = Monday, Friday
        $current = $from->copy();

        for ($i = 0; $i <= 7; $i++) {
            $dayOfWeek = (int) $current->format('N');
            if (in_array($dayOfWeek, $days)) {
                return $current->startOfDay();
            }
            $current->addDay();
        }

        return $from->copy(); 


}

}