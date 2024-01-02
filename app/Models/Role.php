<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];   

    public function permission()
    {
        return $this->belongsToMany(Permission::class,'permission_roles');
    }

    public function admin()
    {
        return $this->belongsToMany(AdminEmployee::class, 'admin_employee_roles')->withPivot('status');
    }

    public function roleable()
    {
        return $this->morphTo();
    }

    public function employees()
    {
        return $this->belongsToMany(AdminEmployee::class, 'employee_roles', 'role_id', 'admin_employee_id');
    }
}
