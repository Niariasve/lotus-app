<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactPlatform extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'is_active',
    ];

    public function customerContact(): HasMany
    {
        return $this->hasMany(CustomerContact::class);
    }
}
