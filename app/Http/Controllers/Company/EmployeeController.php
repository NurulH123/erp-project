<?php

namespace App\Http\Controllers\Company;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index()
    {
        $user = User::find(Auth::id());
        $employees = $user->company->employee;

        return response()->json(['status' => 'Ok', 'data' => $employees], 200);
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $dataEmployee = [];
        $idCompany = $user->company->id;
        $company = Company::find($idCompany);

        // create code employee
        $countEmployees = $company->employee->count();

        $numEmployee = 10000000 + $countEmployees;
        $numCompany = 1000 + $idCompany;
        $numUser = 1000 + $user->id;

        $uniqEmployee = (string)substr($numEmployee, 1);
        $uniqIDCompany = (string)substr($numCompany, 1);
        $userInputer = (string)substr($numUser, 1);

        $code =  $userInputer.'-'.$uniqIDCompany.'-'.date('Ymd').'-'.$uniqEmployee;

        try {
            $dataEmployee['code'] = $code;
            $dataEmployee['username'] = $request->username;
            $dataEmployee['isAdmin'] = $request->is_admin;
            $dataProfile =  $request->except('username', 'is_admin');

            // create employee
            $company->employee()->create($dataEmployee);

            return response()->json([
                'status' => 'success', 
                'message' => 'Data Karyawan Berhasil Ditambahkan'
            ], 200);

        } catch (\Throwable $th) {
            throw $th->getMessage();
        }
    }

    /**
     *  Route ini hanya utk testing
     */
    public function destroy(Employee $employee)
    {
        try {
            if ($employee->delete()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data Telah Dihapus'
                ]);
            }
        } catch (\Throwable $th) {
            throw $th->getMessage();
        }
        
    }
}
