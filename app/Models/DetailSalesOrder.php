<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailSalesOrder extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function so()
    {
        return $this->belongsTo(SalesOrder::class);
    }
}
