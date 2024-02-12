<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileEmployee extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function status()
    {
        return $this->belongsTo(StatusEmployee::class, 'status_employee_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
