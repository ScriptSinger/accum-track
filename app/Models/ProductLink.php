<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductLink extends Model
{

    protected $fillable = [
        'url',
        'shop_id',
        'processed'

    ];


    public function product()
    {
        return $this->hasOne(Product::class);
    }


    public function shop()
    {
        return $this->belongsTo(Shop::class); // Связь многие к одному
    }
}
