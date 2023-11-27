<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Return_order extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_id',
        'order_id',
        'order_code',
        'order_date',
        'customer_id',
        'product_id',
        'quantity',
        'return_reason',
        'opened',
        'comment',
        'action',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

}
