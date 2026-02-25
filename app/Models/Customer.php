<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'city',
        'phone',
    ];

    public function contactPlatforms(): HasMany
    {
        return $this->hasMany(CustomerContact::class);
    }

    public function primaryContactPlatform(): HasOne 
    {
        return $this->hasOne(CustomerContact::class)->where('is_primary', true);
    }
}
