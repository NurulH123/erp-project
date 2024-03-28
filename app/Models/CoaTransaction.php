<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoaTransaction extends Model
{
    use HasFactory;

    protected  $guarded = ['id'];

    public function invoiceable()
    {
        return $this->morphTo();
    }
}
