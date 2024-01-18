<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function details()
    {
        return $this->hasMany(DetailSalesOrder::class);
    }

    public function invoices()
    {
        return $this->hasMany(InvoiceSalesOrder::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
