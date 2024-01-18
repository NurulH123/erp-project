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
        return $this->hasMany(BranchCompany::class, 'company_id');
    }

    public function employee()      
    {
        return $this->morphMany(Employee::class, 'employiable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function positions()
    {
        return $this->morphMany(Position::class, 'positionable');
    }

    public function roles()
    {
        return $this->morphMany(Role::class, 'roleable');
    }

    public function employeeStatus()
    {
        return $this->morphMany(StatusEmployee::class, 'statusable');
    }

    public function permission()
    {
        return $this->belongsToMany(Permission::class, 'company_permissions');
    }

    public function vendor()
    {
        return $this->morphMany(Vendor::class, 'vendorable');
    }

    public function customer()
    {
        return $this->morphMany(Customer::class, 'customerable');
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function productCategories()
    {
        return $this->hasMany(CategoryProduct::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function transactionPo()
    {
        return $this->hasMany(PurchasingOrder::class);
    }

    public function transactionSo()
    {
        return $this->hasMany(SalesOrder::class);
    }
}
