<?php

namespace App\Http\Controllers;

use App\Models\COA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CoaController extends Controller
{
    public function index()
    {
        $user = auth()->user()->employee;
        $company = $user->company;

        return response()->json([
            'data' => $company->coas
        ]);
    }

    public function allData()
    {
        $user = auth()->user()->employee;
        $company = $user->company;

        $sort = request('sort') ?? '5';

        $coas = COA::where('companiable_id', $company->id)
                            ->paginate($sort);
        
        return response()->json([
            'data' => $coas
        ]);
    }

    /**
     *  Menambahkan Akun Baru Keuangan
     */
    public function store(Request $request)
    {
        $req = $request->only('code', 'name_account', 'category');

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

        return response()->json([
            'status' => 'success',
            'message' => 'Data Telah Disimpan',
            'data' => $coa
        ]);
    }

    public function update(Request $request, COA $coa)
    {
            
    }
}
