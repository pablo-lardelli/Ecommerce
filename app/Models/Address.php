<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'description',
        'district',
        'reference',
        'receiver',
        'receiver_info',
        'default'
    ];

    protected $casts = [
        'receiver_info' => 'array',
        'default' => 'boolean',
    ];
}
