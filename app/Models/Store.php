<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Store extends Model
{
    protected $table = 'stores';

    protected $fillable = [
        'name', 'base_url', 'user_email', 'user_password'
    ];

    // When setting password, encrypt it (so we can decrypt later)
    public function setUserPasswordAttribute($value)
    {
        if (is_null($value) || $value === '') {
            $this->attributes['user_password'] = null;
            return;
        }
        // If value already appears encrypted (avoid double-encrypt) -- optional guard:
        try {
            // try decrypt; if success, assume already encrypted
            Crypt::decryptString($value);
            $this->attributes['user_password'] = $value;
            return;
        } catch (\Throwable $e) {
            // not encrypted -> encrypt now
        }

        $this->attributes['user_password'] = Crypt::encryptString($value);
    }

    // Convenience: returns decrypted password or null
    public function getDecryptedPassword(): ?string
    {
        $v = $this->attributes['user_password'] ?? null;
        if (!$v) return null;
        try {
            return Crypt::decryptString($v);
        } catch (\Throwable $e) {
            return null;
        }
    }

    // Optionally hide the raw encrypted value from array/json forms
    protected $hidden = ['user_password'];
}
