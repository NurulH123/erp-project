<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function customerable()
    {
        return $this->morphTo();
    }

    public function transaksiSo()
    {
        return $this->hasMany(SalesOrder::class);
    }
}
