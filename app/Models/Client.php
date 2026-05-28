<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'lastname',
        'phone',
        'email',
    ];

    public function getFullNameAttribute(): string
    {
        return trim($this->name . ' ' . $this->lastname);
    }
     
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
