<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SalesOrderController extends Controller
{
    public function index()
    {
        $sort = request('sort') ?? 5;

        $user = auth()->user();
        $companyId = $user->company->id;
        $salesOrders = SalesOrder::whereHas('company', function(Builder $query) use($companyId){
                            $query->where('id', $companyId);
                        })->paginate($sort);

        return response()->json([
            'status' => 'success',
            'data' => $salesOrders
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required'
        ]);
    }
}
