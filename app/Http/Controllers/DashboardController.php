<?php

namespace App\Http\Controllers;

use App\Models\PurchasingOrder;
use App\Models\SalesOrder;

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
                // ->selectRaw('sum(total_pay) as sum')
                ->get();
        $so = SalesOrder::where('company_id', $company->id)
                ->get();
        $statPo = $this->statisticPo($po);
        
    }

    public function statisticPo($po)
    {

    }
}
