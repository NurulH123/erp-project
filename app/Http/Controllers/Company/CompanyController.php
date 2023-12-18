<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        // Private
        $companies = Company::all();

        return response()->json(['status' => 'success', 'data' => $companies]);
    }

    public function create(CompanyRequest $request)
    {
        $user = auth()->user();
        
        $data = $request->validated();
        $data['email'] = $request->email ?? $request->email;

        // create coompany
        $company = $user->company()->create($data); 

        // create admin employee
        $company->employee()->create([
            'username' => $user->username,
            'code' => $user->adminEmployee->code, 
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Perusahaan Berhasil Dibuat',
        ], 200);
    }

    public function update(Request $request, Company $company)
    {
        $user = auth()->user();
        $data = $request->all();

        $updCompany = $user->company->update($data);
        if ($updCompany) {
            return response()->json([
                'status' => 'success',
                'message' => 'Data Berhasil Diupdate',
            ], 200);
        }
    }

    public function changeStatus()
    {
        $user = auth()->user();
        $status = $user->company->status;

        $user->company()->update(['status' => !$status]);
        $IStatus = $status ? 'Aktif' : 'Tidak Aktif';

        return response()->json([
            'status' => 'success', 
            'message' => 'Perusahaan '.$IStatus
        ], 200);
    }

    /**
     *  Route ini hamya utk testing
     */
    public function destroy(Company $company)
    {
        if ($company->delete())  {
            return response()->json(['status' => 'success', 'message' => 'Perusahaan Dihapus']);
        }

        return response()->json(['status' => 'failed', 'message' => 'Terjadi Kesalahan']);
    }
}
