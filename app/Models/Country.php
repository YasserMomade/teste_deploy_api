<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'iva', 'coin'];
    
    protected $casts= [
        'iva' => 'decimal:2',
    ];

    public function counters()
    {
        return $this->hasMany(Counter::class);
    }

}
