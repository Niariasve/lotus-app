<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierOrderStatus extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(SupplierOrder::class, 'status_id');
    }
}
