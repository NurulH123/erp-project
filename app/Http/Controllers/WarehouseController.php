<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WarehouseController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $warehouses = $user->company->warehouses;

        return response()->json([
            'status' => 'success', 
            'data' => $warehouses
        ]);

    }

    public function show(Warehouse $warehouse)
    {
        return response()->json([
            'status' => 'success',
            'data' => $warehouse
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

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
