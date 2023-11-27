<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'sumber_lead',
        'jumlah_lead',
        'user_id',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->hasMany(Order::class)->whereDate('created_at', $this->created_at->toDateString());
    }

}
