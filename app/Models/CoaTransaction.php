<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CoaTransaction extends Model
{
    use HasFactory;

    protected  $guarded = ['id'];

    public function invoiceable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function debet()
    {
        return $this->belongsTo(COA::class, 'debet', 'id');
    }

    public function kredit()
    {
        return $this->belongsTo(COA::class, 'kredit', 'id');
    }
}
