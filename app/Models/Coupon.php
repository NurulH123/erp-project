<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'category',
        'discount',
        'date_start',
        'date_end',
        'coupon_uses',
        'customer_uses',
        'status',
    ];

    public function order()
    {
        return $this->hasMany(Order::class);
    }
}
