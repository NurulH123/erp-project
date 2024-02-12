<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryPo extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function po()
    {
        return $this->belongsTo(PurchasingOrder::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
