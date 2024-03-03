<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\PurchasingOrder;
use App\Models\SalesOrder;
use App\Models\Vendor;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user()->employee;
        $company = $user->company;

        $po = PurchasingOrder::where([
                    ['status', '=', 'accepted'],
                    ['company_id', '=', $company->id]
                ])
                ->get();
        $so = SalesOrder::where('company_id', $company->id)->get();
        $vendor = Vendor::where('vendorable_id', $company->id)->get();
        $customer = Customer::where('customerable_id', $company->id)->get();
        $product = Product::where('company_id', $company->id)->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'purchase_order' => count($po),
                'sales_order' => count($so),
                'vendor' => count($vendor),
                'customer' => count($customer),
                'product' => count($product)
            ]
        ]);
        
    }
}
