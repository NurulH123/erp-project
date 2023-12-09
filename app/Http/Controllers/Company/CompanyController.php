<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function create(CompanyRequest $request)
    {
        $data = $request->validate();
        $data['email'] = $request->email ?? $request->email;

        if (Company::create($data)) {
            return response()->json(['message' => 'Perusahaan Berhasil Dibuat'], 200);
        }
    }

    public function update(Request $request)
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
}
