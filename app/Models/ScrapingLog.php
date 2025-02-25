<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapingLog extends Model
{
    use HasFactory;
    protected $fillable = ['shop_id', 'status', 'message'];
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
