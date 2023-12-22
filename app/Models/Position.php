<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function positionable()
    {
        return $this->morphTo();
    }

    public function profileEmployee()
    {
        return $this->hasOne(ProfileEmployee::class);
    }
}
