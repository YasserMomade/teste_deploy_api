<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Counter extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'country_id', 'address'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

        public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
