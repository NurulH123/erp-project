<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    private $user, $company;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    public function index()
    {
        // Private
        $companies = Company::all();

        return response()->json(['status' => 'success', 'data' => $companies]);
    }

    public function create(CompanyRequest $request)
    {
        $user = auth()->user();
        
        // validation
        $validator  = Validator::make($request->all(), [
            'name'  => 'required|min:3',
            'category' => 'required',
            'address' => 'required',
            'phone' =>' required',
        ],[
            'name.required' => 'Nama Harus Diisi',
            'address.required' => 'Alamat Harus Diisi',
            'phone.required' => 'Telepon Harus Diisi',
            'category.required' => 'Kategori Harus Diisi',
            'name.min' => 'Minimal 3 Karakter',
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Terjadi Kesalahan',
                'data' => $validator->errors()
            ], 401);
        }

        // create coompany
        $company = $user->company()->create($request->all()); 

        if (!$user->adminEmployee) {
            // create admin employee
            $company->employee()->create([
                'username' => $user->username,
                'code' => $user->adminEmployee->code, 
            ]);
        }

        // response
        return response()->json([
            'status' => 'success',
            'message' => 'Perusahaan Berhasil Dibuat',
            'data' =>  $user->company
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
                'data' => $user->company
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
