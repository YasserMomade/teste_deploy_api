<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Costumer extends Model
{
    protected $fillable = [
        'name',
        'lastname',
        'phone',
        'email',
    ];

    public function orderRequest() {
        return $this->hasMany(Orders_request::class);
    }
}
