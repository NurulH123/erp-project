<?php

namespace App\Models;

use App\Models\DetailPurchasingOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoicePurchaseOrder extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function detail()
    {
        return $this->belongsTo(DetailPurchasingOrder::class);
    }
}
