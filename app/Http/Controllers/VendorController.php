<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    public function index()
    {
        $sort = request('sort') ?? 5;
        $vendors = Vendor::whereHas('vendorable', function(Builder $query) {
            $user = auth()->user();
            $companyId = $user->company->id;

            $query->where('id', $companyId);
        })->paginate($sort);

        return response()->json([
            'status' => 'success',
            'data' => $vendors,
        ]);
    }

    public function show(Vendor $vendor)
    {
        return response()->json([
            'status' => 'success',
            'data' => $vendor,
        ]);
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'name'  => 'required',
            'phone' => 'required|unique:vendors,phone',
            'address' => 'required'
        ],[
            'name.required' => 'Nama Harus Diisi',
            'phone.required' => 'Telepon Harus Diisi',
            'phone.unique' => 'Nomor Telepon Sudah Ada',
            'address.required' => 'Alamat Harus Diisi', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ], 442);
        }

        $data = $request->all();
        $vendor = $user->company->vendor()->create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Telah Ditambahkan',
            'data' => $vendor,
        ]);
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required',
            'phone' => 'required|unique:vendors,id,'.$vendor->id,
            'address' => 'required'
        ],[
            'name.required' => 'Nama Harus Diisi',
            'phone.required' => 'Telepon Harus Diisi',
            'phone.unique' => 'Nomor Telepon Sudah Ada',
            'address.required' => 'Alamat Harus Diisi', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ], 442);
        }

        $data = $request->all();
        $vendor->update($data);

        $updVendor = Vendor::find($vendor->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Telah Diubah',
            'data' => $updVendor,
        ]);
    }

    public function changeStatus(Vendor $vendor)
    {
        $status = $vendor->is_active;

        $vendor->update(['is_active' => !$status]);
        $IStatus = $status ? 'Aktif' : 'Tidak Aktif';

        return response()->json([
            'status' => 'success', 
            'is_status' => $status,
            'message' => 'Vendor '.$IStatus
        ]);
    }
}
