<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DiscountRequest extends Model
{
    protected $guarded = [];
    public function items() {
        return $this->hasMany(DiscountRequestItem::class);
    }
}
