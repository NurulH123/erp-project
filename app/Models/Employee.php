<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function company()
    {
        return $this->morphTo();
    }

    public function profile()
    {
        return $this->hasOne(ProfileEmployee::class, 'employee_id');
    }
}
