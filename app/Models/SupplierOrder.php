<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierOrder extends Model
{
    protected $fillable = [
        'order_number',
        'supplier_id',
        'status_id',
        'tracking',
        'ordered_at',
        'shipped_at',
        'arrived_at',
    ];

    protected function casts(): array
    {
        return [
            'supplier_id' => 'integer',
            'status_id' => 'integer',
            'ordered_at' => 'date',
            'shipped_at' => 'date',
            'arrived_at' => 'date',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(SupplierOrderStatus::class, 'status_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SupplierOrderItem::class);
    }
}
