<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class COA extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function addition()
    {
        return $this->hasOne(CoaAddition::class, 'coa_id');
    }
}
