<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders_request extends Model
{
    protected $fillable = [
        'description',
        'quantity',
        'costumer_id',
        'store_id'
    ];

    public function costumer() {
        return $this->belongsTo(Costumer::class);
    }
}
