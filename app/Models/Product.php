<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'id',
        'sku',
        'name',
        'description',
        'brand',
        'line',
        'height',
        'weight_est',
        'weight_real',
        'release_date',
    ];
}
