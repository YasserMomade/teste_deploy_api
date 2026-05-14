<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'document_type',
        'url',
        'responsible_id',
        'order_id',
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }
}