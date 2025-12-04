<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountRequest extends Model
{
    protected $table = 'discount_requests';

    protected $casts = [
        'items_json' => 'array',
        'subtotal' => 'float',
        'total_vat' => 'float',
        'total_payable' => 'float',
        'discount_requested' => 'float',
    ];

    protected $fillable = [
        'temp_cart_id','store_name','salesman','customer_mobile','sales_type',
        'items_json','subtotal','total_vat','total_payable','discount_requested','total_after_discount',
        'pos_callback_url','status','admin_id','store_name'
    ];
}
