<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AdminRole extends Pivot
{
    use HasFactory;

    protected $guarded =  ['id'];
    protected $table = 'admin_employee_roles';

    public function roles()
    {
        return $this->belongsTo(Role::class);
    }

    public function admin()
    {
        return $this->belongsTo(AdminEmployee::class);
    }
}
