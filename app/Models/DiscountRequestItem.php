<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DiscountRequestItem extends Model
{
    protected $guarded = [];
    public function request() {
        return $this->belongsTo(DiscountRequest::class, 'discount_request_id');
    }
}
