<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;

class BranchCompanyController extends Controller
{
    public function createBranch(CompanyRequest $request)
    {
        $company = auth()->user->company;
        $data = $request->validate();

        $branch = $company->branch->create($data);

        if ($branch) {
            return response()->json(['message' => 'Data Berhasil Disimpan']);
        }
    }

    public function update(Request $request)
    {
        $company = auth()->user->company;
        $data = $request->all();

        $branch = $company->branch->update($data);

        if ($branch) {
            return response()->json(['message' => 'Data Berhasil Diubah'], 200);
        }
    }
}
