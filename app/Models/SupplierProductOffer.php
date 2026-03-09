<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierProductOffer extends Model
{
    protected $fillable = [
        'supplier_id',
        'product_id',
        'base_cost',
        'currency',
        'estimated_tax',
        'estimated_shipping',
        'other_fees',
        'is_available',
        'last_checked_at',
    ];
}
