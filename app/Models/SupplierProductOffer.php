<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
