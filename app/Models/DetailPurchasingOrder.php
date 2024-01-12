<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPurchasingOrder extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function po()
    {
        return $this->belongsTo(PurchasingOrder::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
