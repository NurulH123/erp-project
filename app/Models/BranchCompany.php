<?php

namespace App\Models;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchCompany extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function employee()
    {
        return $this->morphMany(Employee::class, 'employiable');
    }

    public function roles()
    {
        return $this->morphMany(Role::class, 'roleable');
    }

    public function employeeStatus()
    {
        return $this->morphMany(StatusEmployee::class, 'statusable');
    }
}
