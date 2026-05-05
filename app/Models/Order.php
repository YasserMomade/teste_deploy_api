<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'client_id',
        'description',
        'tracking',
        'origin',
        'destination',
        'reception_date',
        'service_type',
        'volume_number',
        'weight',
        'declared_weight',
	    'category_id',
        'responsible_id',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
