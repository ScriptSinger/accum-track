<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'shop_id',
        'category_id',
        'name',
        'voltage',
        'capacity',
        'cca',
        'polarity',
        'terminal_type',
        'bottom_fixation',
        'size_standard',
        'technology',
        'dimensions',
        'origin',
        'brand',
        'country',
        'serviceable'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }
}
