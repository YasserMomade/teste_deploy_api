<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{

   protected $fillable = [
        'name'
    ];

     public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
