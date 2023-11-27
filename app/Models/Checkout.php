<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'product_id',
        'quantity',
        'price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_code', 'order_code');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeFilter($query, $request)
    {
        if ($request->sumber_lead) {
            $key = $request->sumber_lead;
            $query->where('sumber_lead', 'like', "%$key%");
        }

        if ($request->product_name) {
            $key = $request->product_name;
            // $query->where('products.name', 'like', "%$key%");
            $query->whereHas('product', function($query) use ($key) {
                $query->where('name', 'like', "%$key%");
            });
        }

        if ($request->username) {
            $key = $request->username;
            // $query->where('products.name', 'like', "%$key%");
            $query->whereHas('user', function($query) use ($key) {
                $query->where('username', 'like', "%$key%");
            });
        }

        if ($request->date) {
            $key = $request->date;
            $query->whereDate('created_at',  "$key");
        }

        return $query;
    }
}
