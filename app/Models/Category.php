<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tag',
        'description',
        'category_status',
    ];

    public function product()
    {
        return $this->hasMany(Product::class);
    }
}
