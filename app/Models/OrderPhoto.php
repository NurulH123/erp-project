<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'order_code',
        'phone',
        'path',
        'image_name',

    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
