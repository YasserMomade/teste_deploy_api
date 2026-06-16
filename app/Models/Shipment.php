<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    protected $fillable = [
        'reference',
        'carta_porte',
        'airline',
        'origin',
        'destination',
        'shipment_date',
        'general_observations',
        'responsible_id',
    ];

    protected function casts(): array
    {
        return [
            'shipment_date' => 'date',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }
}
