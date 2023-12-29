<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function role()
    {
        return $this->belongsToMany(Role::class,'permission_roles');
    }

    public function children()
    {
        return $this->hasMany(Permission::class, 'permission_group_id', 'id');
    }

    public function company()
    {
        return $this->belongsToMany(Company::class, 'company_permissions');
    }
}
