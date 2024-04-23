<?php

namespace App\Http\Controllers\Company;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\COA;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('hasCompany')->except('create');
    }

    public function show()
    {
        // Private
        $user = auth()->user()->employee;
        $company = Company::with('permission.children')->where('id', $user->company->id)->first();

        return response()->json(['status' => 'success', 'data' => $company]);
    }

    public function listAll()
    {
        // Private
        // $companies = Company::all();

        // return response()->json(['status' => 'success', 'data' => $companies]);
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        
        // validation
        $validator  = Validator::make($request->all(), [
            'name'  => 'required|min:3',
            'category' => 'required',
            'address' => 'required',
            'phone' =>' required|unique:companies,phone',
        ],[
            'name.required' => 'Nama Harus Diisi',
            'address.required' => 'Alamat Harus Diisi',
            'phone.required' => 'Telepon Harus Diisi',
            'phone.unique' => 'Maaf, Nomor Telepon Sudah Ada',
            'category.required' => 'Kategori Harus Diisi',
            'name.min' => 'Minimal 3 Karakter',
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ], 442);
        }

        $data = $request->all();

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = date('YmdHis').'.'.$file->getClientOriginalExtension();
            $file->move('uploads/logo/company', $filename);

            $data['logo'] = 'uploads/logo/company/'.$filename;
        }

        // create coompany
        $company = $user->company()->create($data); 

        // create employee
        $company->employee()->create([
            'username' => $user->username,
            'email' => $user->email,
            'is_admin'  => true,
            'code' => $user->adminEmployee->code, 
        ]);

        // Create Default Akun CoA
        $accounts = [
            [
                'code' => 1110,
                'name_account' => 'Kas',
            ],
            [
                'code' => 1120,
                'name_account' => 'Bank',
            ],
            [
                'code' => 1130,
                'name_account' => 'Piutang Dagang',
            ],
            [
                'code' => 2110,
                'name_account' => 'Hutang Dagang',
            ],
            [
                'code' => 4100,
                'name_account' => 'Penjualan'
            ],
            [
                'code' => 5100,
                'name_account' => 'Pembelian'
            ],
        ];

        $collAccounts = collect($accounts);
        $collAccounts->each(function($item) use($company) {
            $company->coas()->create($item);
        });

        // response
        return response()->json([
            'status' => 'success',
            'message' => 'Perusahaan Berhasil Dibuat',
            'data' =>  $user->company
        ], 200);
    }

    public function update(Request $request, Company $company)
    {
        $user = auth()->user()->employee;
        $data = $request->all();

        if ($request->hasFile('logo')) {

            if (!is_null($company->logo)) {
                unlink($company->logo);
            }

            $file = $request->file('logo');
            $filename = date('YmdHis').'.'.$file->getClientOriginalExtension();
            $file->move('uploads/logo/company', $filename);

            $data['logo'] =  'uploads/logo/company/'.$filename;
        }

        $updCompany = $company->update($data);
        
        if ($updCompany) {
            return response()->json([
                'status' => 'success',
                'message' => 'Data Berhasil Diupdate',
                'data' => $user->company
            ], 200);
        }
    }

    public function changeStatus()
    {
        $user = auth()->user()->employee;
        $status = $user->company->status;

        $user->company()->update(['status' => !$status]);
        $IStatus = $status ? 'Aktif' : 'Tidak Aktif';

        return response()->json([
            'status' => 'success', 
            'is_status' => $status,
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
