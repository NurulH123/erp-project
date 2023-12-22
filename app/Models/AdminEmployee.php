<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminEmployee extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'employee_roles', 'admin_employee_id', 'role_id');
    }
}
