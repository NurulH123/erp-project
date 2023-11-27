<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Http\Controllers\CustomerController;
use App\Models\Address;
use App\Models\Checkout;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\History;
use App\Models\OrderPhoto;
use App\Models\OrderReport;
use App\Models\Return_order;
use App\Models\Sending;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderReportController extends Controller
{
    public function order_report()
    {
        $data = OrderReport::all();
        return response()->json(['data' => $data], 200);
    }
}
