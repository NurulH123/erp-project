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
        return $this->belongsToMany(Role::class, 'admin_employee_roles')
                    ->withPivot('id','status','admin_employee_id',  'role_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
