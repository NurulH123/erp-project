<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function category()
    {
        return $this->belongsTo(CategoryProduct::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'product_warehouses')
                ->withPivot('stock', 'id');
    }

    public function product()
    {
        return $this->belongsToMany($this, 'boms', 'material_id', 'product_id')
                ->withPivot('need');
    }

    public function materials()
    {
        return $this->belongsToMany($this, 'boms', 'product_id',  'material_id')
                ->withPivot('need');
    }

    public function detail_so()
    {
        return $this->hasMany(DetailSalesOrder::class);
    }
}
