<?php

namespace App\Models;

use App\Models\Invoice;
use App\Models\DetailPurchasingOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchasingOrder extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function details()
    {
        return $this->hasMany(DetailPurchasingOrder::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, DetailPurchasingOrder::class);
    }
}
