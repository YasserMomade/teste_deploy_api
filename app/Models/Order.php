<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'client_id',
        'description',
        'tracking',
        'origin',
        'destination',
        'reception_date',
        'service_type',
        'volume_number',
        'weight',
        'declared_weight',
	    'category_id',
        'responsible_id',
        'invoice_id',
        'store_id',
    ];

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

    public function file()
    {
        return $this->hasMany(File::class);
    }
}
