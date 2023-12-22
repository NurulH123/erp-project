<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusEmployee extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function statusable()
    {
        return $this->morphTo();
    }

    public function profileEmployee()
    {
        return $this->hasMany(ProfileEmployee::class, 'status_id');
    }
}
