<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryLink extends Model
{
    protected $fillable = [
        'category_name',
        'category_url',
        'shop_id',
        'category_id'
    ];

    public function productLinks()
    {
        return $this->hasMany(ProductLink::class);
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
