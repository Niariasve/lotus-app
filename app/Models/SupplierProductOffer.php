<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierProductOffer extends Model
{
    protected $fillable = [
        'supplier_id',
        'product_id',
        'base_cost',
        'priority',
        'retail_price',
        'profit_percentage',
        'url',
        'is_available',
        'last_checked_at',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
