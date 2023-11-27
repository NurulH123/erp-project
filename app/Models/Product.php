<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tag',
        'description',
        'model',
        'category_id',
        'price',
        'quantity',
        'minimum_quantity',
        'product_status',
        'weight',
        'weight_class',
        'image',
    ];

    public function order()
    {
        return $this->hasMany(Order::class);
    }

    public function category()
    {
        return $this->hasManyThrough(Category::class, ProductCategory::class, 'product_id', 'id','id', 'category_id' );
    }

    public function checkout()
    {
        return $this->belongsToMany(Checkout::class);
    }

    public function productcategory()
    {
        return $this->hasMany(ProductCategory::class, 'product_id', 'id');
    }
}
