<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = [
        'amount',
        'min_weight',
        'max_weight',
        'category_id',
    ];

    public function category() 
    {
        return $this->belongsTo(Category::class);
    }
}
