<?php

namespace App\Http\Controllers;

use App\Models\COA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CoaController extends Controller
{
    /**
     *  Menambahkan Akun Baru Keuangan
     */
    public function store(Request $request)
    {
        $user = auth()->user()->employee;
        $company = $user->company;
        $classname = get_class($company);

        $req = $request->only('code', 'name_account');

        $validator = Validator::make($req, [
            'code' => 'required',
            'name_account' => 'required'
        ], [
            'code.required' => 'Kode Akun Harus Diisi',
            'name_account' => 'Nama Akun Harus Diisi'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ]);
        }

        $coa = COA::create($req);
        
        $coa->addition()->create([
            'companiable_id' => $company->id,
            'companiable_type' => $classname
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Telah Disimpan',
            'data' => $coa
        ]);
    }
}
