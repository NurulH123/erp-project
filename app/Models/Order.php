<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',
        'customer_id',
        'address_id',
        'coupon_id',
        'sub_total',
        'ongkos_kirim',
        'potongan_ongkir',
        'final_ongkir',
        'total_price',
        'payment_id',
        'sending_id',
        'description',
        'nomor_resi',
        'tanggal_resi',
        'nama_rekening',
        'special',
        'sumber_lead',
        'jenis_lead',
        'kode',
        'note',
        'status',
        'created_at',
        'expedition'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function checkout()
    {
        return $this->hasMany(Checkout::class, 'order_code', 'order_code');
    }

    public function product()
    {
        return $this->hasManyThrough(Product::class, Checkout::class, 'order_code', 'id','order_code', 'product_id' );
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
    public function sending()
    {
        return $this->belongsTo(Sending::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function orderPhotos()
    {
        return $this->hasMany(OrderPhoto::class);
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

        if ($request->created_by) {
            $key = $request->created_by;
            // $query->where('products.name', 'like', "%$key%");
            $query->whereHas('user', function($query) use ($key) {
                $query->where('username', 'like', "%$key%");
            });
        }

        if ($request->date) {
            $key = $request->date;
            $query->whereDate('created_at',  "$key");
        }

        $query->where('special', 'false');

        return $query;
    }
}
