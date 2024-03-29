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
        $month = request('month') ?? date('m');

        $po = PurchasingOrder::where([
                    ['status', '=', 'accepted'],
                    ['company_id', '=', $company->id]
                ])
                ->get();
        $so = SalesOrder::where('company_id', $company->id)->get();
        $vendor = Vendor::where('vendorable_id', $company->id)->get();
        $customer = Customer::where('customerable_id', $company->id)->get();
        $product = Product::where('company_id', $company->id)->get();

        $poGroupping = $po->groupBy('date_accepted');
        $soGroupping = $so->groupBy('date_transaction');

        return response()->json([
            'status' => 'success',
            'data' => [
                'purchase_order' => count($po),
                'sales_order' => count($so),
                'vendor' => count($vendor),
                'customer' => count($customer),
                'product' => count($product),
                'diagram' => [
                    'so' => $this->dataDiagram($soGroupping, $month),
                    'po' => $this->dataDiagram($poGroupping, $month),
                ]

            ]
        ]);
        
    }

    public function dataDiagram($datas, $month)
    {
        $newData = [];

        foreach ($datas as $date => $value) {
            if (date('m', strtotime($date) == $month)) {
                $newData = array_merge($newData, [$date => $value->sum('total_pay')]);
            }
        }

        return $newData;
    }
}
