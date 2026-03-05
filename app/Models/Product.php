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

    protected function casts(): array 
    {
        return [
            'height' => 'decimal:3',
            'weight_est' => 'decimal:3',
            'weight_real' => 'decimal:3',
            'release_date' => 'date:Y-m-d',
        ];
    }
}
