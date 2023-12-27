<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Models\BranchCompany;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Support\Facades\Validator;

class BranchCompanyController extends Controller
{
    public function index()

    {
        $company = Company::find(1);
        // $user = auth()->user();
        // $branch = $user->company->branch;
        dd($branch);

        return response()->json([
            'status' => 'success',
            'data'  => $branch
        ]);
    }

    public function show(BranchCompany $branch)
    {
        return response()->json([
            'status' => 'success',
            'data' => $branch,
        ]);
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'address'   => 'required',
            'email'     => 'required|unique:branch_companies,email',
            'phone'     => 'required|sometimes|unique:branch_companies,phone',
        ], [
            'name.required'      => 'Nama Harus Diisi',
            'address.required'   => 'Alamat Harus Diisi',
            'email.required'     => 'Email Harus Diisi',
            'email.unique'       => 'Email Sudah Ada',
            'phone.required'     => 'Telepon Harus Diisi',
            'phone.unique'       => 'Telepon Sudah Terdaftar',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ], 442);
        }

        $data = $request->all();

        $branch = $user->company->branch()->create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Telah Ditambahkan',
            'data' => $branch
        ]);
    }

    public function update(Request $request, BranchCompany $branch)
    {
        $user = auth()->user(); 

        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'address'   => 'required',
            'email'     => 'required|unique:branch_companies,email',
            'phone'     => 'required|unique:branch_companies,phone',
        ], [
            'name.required'      => 'Nama Harus Diisi',
            'address.required'   => 'Alamat Harus Diisi',
            'email.required'     => 'Email Harus Diisi',
            'email.unique'       => 'Email Sudah Ada',
            'phone.required'     => 'Telepon Harus Diisi',
            'phone.unique'       => 'Telepon Sudah Terdaftar',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ], 442);
        }

        $data = $request->all();

        $user->company->branch->update($data);
        $branch = BranchCompany::find($branch->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Berhasil Diubah',
            'data' => $branch,
        ]);
    }

    public function changeStatus(BranchCompany $branch)
    {
        $status = $branch->is_status;

        $branch->update(['is_active' => !$status]);
        $statusText = $status ? 'Aktif' : 'Tidak Aktif';

        return response()->json([
            'status' => 'success', 
            'is_status' => $status,
            'message' => 'Vendor '.$statusText
        ]);
    }
}
