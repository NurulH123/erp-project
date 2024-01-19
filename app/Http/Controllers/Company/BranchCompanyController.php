<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Models\BranchCompany;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BranchCompanyController extends Controller
{
    public function index()

    {
        $company = Company::find(1);
        $user = auth()->user();
        $branch = $user->company->branch;

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
            'email'     => 'required|sometimes|unique:branch_companies,email',
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
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'address'   => 'required',
            'email'     => 'sometimes|required|email|unique:branch_companies,id,'.$branch->id,
            'phone'     => 'sometimes|required|unique:branch_companies,id,'.$branch->id,
            Rule::unique('branch_companies')->ignore($branch),
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

        $branch->update($data);
        $branch = BranchCompany::find($branch->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Berhasil Diubah',
            'data' => $branch,
        ]);
    }

    public function changeStatus(BranchCompany $branch)
    {
        $status = $branch->status;

        $branch->update(['status' => !$status]);
        $statusText = $status ? 'Aktif' : 'Tidak Aktif';

        return response()->json([
            'status' => 'success', 
            'is_status' => $status,
            'message' => 'Branch '.$statusText
        ]);
    }
}
