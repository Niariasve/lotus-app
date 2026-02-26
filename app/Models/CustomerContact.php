<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerContact extends Model
{
    protected $fillable = [
        'customer_id',
        'platform_id',
        'contact_identifier',
        'is_primary',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function contactPlatform(): BelongsTo
    {
        return $this->belongsTo(ContactPlatform::class, 'platform_id');
    }
}
