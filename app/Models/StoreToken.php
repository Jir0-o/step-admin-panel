<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreToken extends Model
{
    protected $table = 'store_tokens';

    public $timestamps = false; 

    protected $fillable = [
        'store_id',
        'user_id',
        'token',
        'expires_at',
        'meta',
        'created_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'meta' => 'array',
    ];
}
