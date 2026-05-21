<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'status';

    protected $fillable = [
        "descryption",
        "responsible_id",
        "order_id",
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function responsible()
{
    return $this->belongsTo(User::class, 'responsible_id');
}

}
