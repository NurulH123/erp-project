<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'full_address',
        'province',
        'city',
        'district',
        'province_id',
        'city_id',
        'district_id',
        'postal_code',
    ];

    public function customer()
    {
        return $this->hasMany(Customer::class);
    }

}
