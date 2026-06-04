<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{

use HasFactory;

    protected $fillable = [
        'client_id',
        'description',
        'tracking',
        'pick_up_code',
        'reception_date',
        'weight',
        'declared_weight',
        'sync',
	    'category_id',
        'responsible_id',
        'invoice_id',
        'store_id'
    ];

    protected function casts(): array
    {
        return [
            'reception_date' => 'datetime',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function status()
    {
        return $this->hasMany(Status::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

      public function statuses(): HasMany
    {
        return $this->hasMany(Status::class, 'order_id');
    }

      public function latestStatus(): HasOne
    {
        return $this->hasOne(Status::class, 'order_id')->latestOfMany();
    }
    
    public function observations(): HasMany
    {
        return $this->hasMany(Observation::class);
    }


    public function file()
    {
        return $this->hasMany(File::class);
    }

}