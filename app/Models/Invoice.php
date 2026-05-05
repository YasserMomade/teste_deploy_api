<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
   protected $fillable = [
        'amountTo_pay',
        'amount_paid',
        'payment_status',
        'payment_method',	 
        'order_id',	 
   ];

   public function order() {
    return $this->belongsTo(Order::Class);
   }
}
