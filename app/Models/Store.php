<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Store extends Model
{
    protected $table = 'stores';

    protected $fillable = [
        'name',
        'base_url',
        'login_api_url',
        'user_email',
        'user_password'
    ];

    /**
     * Mutator – encrypt password on set
     */
    public function setUserPasswordAttribute($value)
    {
        // Empty → do not save anything
        if (is_null($value) || $value === '') {
            $this->attributes['user_password'] = null;
            return;
        }

        // Encrypt every new raw password consistently
        $this->attributes['user_password'] = Crypt::encryptString($value);
    }

    /**
     * Helper: Get decrypted password cleanly
     */
    public function getDecryptedPassword(): ?string
    {
        if (empty($this->attributes['user_password'])) {
            return null;
        }

        try {
            return Crypt::decryptString($this->attributes['user_password']);
        } catch (\Throwable $e) {
            return null; // corrupted / invalid encryption
        }
    }

    /**
     * Hide encrypted password in JSON output
     */
    protected $hidden = ['user_password'];
}
