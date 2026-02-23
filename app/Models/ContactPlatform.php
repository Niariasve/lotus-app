<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactPlatform extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'is_active',
    ];
}
