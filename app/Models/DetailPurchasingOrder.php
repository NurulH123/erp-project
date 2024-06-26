<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailPurchasingOrder extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function po()
    {
        return $this->belongsTo(PurchasingOrder::class);
    }

    public function invoices()
    {
        return $this->hasMany(InvoicePurchaseOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
