<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'url'];


    public function categoryLinks()
    {
        return $this->hasMany(CategoryLink::class);
    }


    public function products()
    {
        return $this->hasMany(Product::class);
    }


    public function productLinks()
    {
        return $this->hasMany(ProductLink::class);
    }
}
