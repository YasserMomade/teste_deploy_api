<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'category',
    ];

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

}
