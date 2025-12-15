<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    public function isNearExpiry(int $thresholdSeconds = 300): bool
    {
        if (! $this->expires_at) {
            return false;
        }

        return Carbon::now()->diffInSeconds($this->expires_at, false) <= $thresholdSeconds;
    }
}
