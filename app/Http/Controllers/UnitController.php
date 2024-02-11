<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    public function index()
    {
        $sort = request('sort') ??  '5';
        $units = Unit::whereHas('company', function(Builder $query) {
            $user = auth()->user();
            $companyId = $user->company->id;

            $query->where('id', $companyId);
        })->paginate($sort);

        return response()->json([
            'status' => 'success',
            'data' => $units,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        $validator = Validator::make($request->all(), [
            'name'  => 'required',
            'code'  => 'required',
        ], [
            'name.required' => 'Nama Harus Diisi',
            'code.required' => 'Kode Harus Diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors(),
            ], 442);
        }

        $data = $request->only('name', 'code');
        $unit = $company->units()->create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Berhasil ditambahkan',
            'data'  => $unit,
        ]);
    }

    public function show(Unit $unit) 
    {
        return response()->json([
            'status' => 'success',
            'data' => $unit,
        ]);
    }

    public function update(Request $request, Unit $unit)
    {
        $user = auth()->user();
        $company = $user->company;

        $validator = Validator::make($request->all(), [
            'name'  => 'required',
            'code'  => 'required',
        ], [
            'name.required' => 'Nama Harus Diisi',
            'code.required' => 'Kode Harus Diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors(),
            ], 442);
        }

        $data = $request->only('name', 'code');
        $unit->update($data);

        $updUnit = Unit::find($unit->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Berhasil Diubah',
            'data'  => $updUnit,
        ]);
    }

    public function changeStatus(Unit $unit)
    {
        $status = !$unit->status;
        $statusText = $status ? 'Diaktifkan' : 'Dinonaktifkan';

        $unit->update(['status' => $status]);

        return response()->json([
            'status' => 'success',
            'message' => $unit->name.' '.$statusText
        ]);
    }
}
