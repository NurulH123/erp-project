<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function branch()
    {   
        return $this->hasMany(BranchCompany::class);
    }

    public function employee()      
    {
        return $this->morphMany(Employee::class, 'employiable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
