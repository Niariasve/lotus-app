<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerContact extends Model
{
    protected $fillable = [
        'customer_id',
        'platform_id',
        'contact_identifier',
        'is_primary',
    ];
}
