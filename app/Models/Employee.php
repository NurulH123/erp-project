<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $hidden = ['password'];

    public function company()
    {
        return $this->morphTo('employiable');
    }

    public function profile()
    {
        return $this->hasOne(ProfileEmployee::class, 'employee_id');
    }

    // public function adminEmployee()
    // {
    //     return $this->hasOne(AdminEmployee::class, 'employee_id');
    // }
}
