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
        $companies = Company::all();

        return response()->json(['status' => 'Ok', 'data' => $companies]);
    }

    public function create(CompanyRequest $request)
    {
        $user = auth()->user();
        
        $data = $request->validated();
        $data['email'] = $request->email ?? $request->email;

        $company = $user->company()->create($data);
        $company->employee()->create(['code' => $user->adminEmployee->code]);

        if ($company) {
            return response()->json(['message' => 'Perusahaan Berhasil Dibuat'], 200);
        }
    }

    public function update(Request $request, Company $company)
    {
        $user = auth()->user();
        $data = $request->all();

        $updCompany = $user->company->update($data);
        if ($updCompany) {
            return response()->json([
                'message' => 'Data Berhasil Diupdate'
            ], 200);
        }
    }

    public function changeStatus()
    {
        $user = auth()->user();
        $status = $user->company->status;

        $user->company()->update(['status' => !$status]);
        $IStatus = $status ? 'Aktif' : 'Tidak Aktif';

        return response()->json(['status' => 'Ok', 'message' => 'Perusahaan '.$IStatus]);
    }

    public function destroy(Company $company)
    {
        if ($company->delete())  {
            return response()->json(['status' => 'Ok', 'message' => 'Perusahaan Dihapus']);
        }

        return response()->json(['status' => 'failed', 'message' => 'Terjadi Kesalahan']);
    }
}
