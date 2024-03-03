<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class WarehouseController extends Controller
{
    public function index()
    {
        $sort =  request('sort') ?? '5';
        $search =  request('search') ?? '';

        $warehouses = Warehouse::whereHas('company', function(Builder $query) {
            $user = auth()->user()->employee;
            $companyId = $user->company->id;

            $query->where('id', $companyId);
        })
        ->where('name', 'like', "%$search%")
        ->paginate($sort);

        return response()->json([
            'status' => 'success', 
            'data' => $warehouses
        ]);
    }

    public function allWarehouse()
    {
        $user = auth()->user()->employee;
        $warehouses = $user->company->warehouses;

        return response()->json([
            'status' => 'success',
            'data' => $warehouses->where('status', true)
        ]);
    }

    public function show(Warehouse $warehouse)
    {
        $warehouse = Warehouse::with('products')
                        ->where('id', $warehouse->id)
                        ->first();

        return response()->json([
            'status' => 'success',
            'data' => $warehouse
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user()->employee;

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'location' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ]);
        }

        $data = $request->only('name', 'location');

        $warehouse = $user->company->warehouses()->create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Berhasil Ditambahkan',
            'data' => $warehouse,
        ]);
    }
    
    public function update(Request $request, Warehouse $warehouse)
    {
        $data = $request->only('name', 'location');

        $warehouse->update($data);
        $warehouse = Warehouse::find($warehouse->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Berhasil Diubah',
            'data' => $warehouse,
        ]);
    }

    public function changeStatus(Warehouse $warehouse)
    {
        $status  = !$warehouse->status;
        $statusText = $status ? 'Diaktifkan' : 'Dinonaktifkan';

        $warehouse->update(['status' => $status]);

        return response()->json([
            'status' => 'success',
            'message' => $warehouse->name.' '.$statusText,
        ]);
    }
}
