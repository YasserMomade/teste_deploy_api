<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileCostumer extends Model
{
    protected $table = 'files_costumer';

    protected $fillable = [
        'document_type',
        'url',
        'costumer_id',
    ];

    public function costumer() {
        return $this->belongsTo(Costumer::class);
    }
}