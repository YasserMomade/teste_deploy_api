<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
   protected $fillable = [
      'amountTo_pay',
      'amount_paid',
      'referencie',
      'payment_status',
      'payment_method',
      'stripe_payment_link',
      'stripe_price_id',
      'paid_at',
      'stripe_payment_intent',
      'stripe_receipt_url',
   ];

   protected $casts = [
    'paid_at'    => 'datetime',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
];

   public function orders()
   {
      return $this->hasMany(Order::class);
   }
}
