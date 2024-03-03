<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Position;
use App\Models\ProductCategory;
use App\Models\Role;
use App\Models\StatusEmployee;
use App\Models\Unit;
use App\Models\Vendor;
use App\Models\Warehouse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
