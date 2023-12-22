<?php

namespace App\Http\Controllers\Company;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

        // Prosess Validasi
        $rules =  [
            'username'  => 'required',
            'gender'    => 'required',
            'phone'     => 'required',
            'address'   => 'required',
        ];

        $messages = [
            'username.required' => 'Nama Harus Diisi',
            'gender.required'   => 'Jenis Kelamin Harus Diisi',
            'phone.required'    => 'Hp Harus Diisi',
            'address.required'  => 'Alamat Harus Diisi',
        ];

        if ($request->is_admin) {
            // Rules user register
            $rules['email'] = 'required|email';
            $rules['password'] = 'required|confirmed';

            // Message user register
            $messages['email.required'] = 'Email Harus Diisi';
            $messages['email.email'] = 'Format Email Tidak Sesuai';
            $messages['password.required'] = 'Password Harus Diisi';
            $messages['password.confirmed'] = 'Konfirmasi Password Tidak Sesuai';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ]);
        }

        // create code employee
        $countEmployees = $company->employee->count();

        $numEmployee = 10000000 + $countEmployees;
        $numCompany = 1000 + $idCompany;
        $numUser = 1000 + $user->id;

        $uniqEmployee = (string)substr($numEmployee, 1);
        $uniqIDCompany = (string)substr($numCompany, 1);
        $userInputer = (string)substr($numUser, 1);

        $code =  $userInputer.'-'.$uniqIDCompany.'-'.date('Ymd').'-'.$uniqEmployee;
        $dataEmployee = $request->only('username');
        $dataEmployee['code'] = $code;

        // Process inputing data employee
        if ($request->is_admin) {
            $dataEmployee = $request->only('username', 'is_admin');
            $dataUser = $request->only('username', 'email', 'password');
            $dataUser['password'] = bcrypt($request->password);

            $user = User::create($dataUser); // Menambahkan data user
            $adminEmployee = $user->adminEmployee()->create(['code' => $code]);// Menambahkan data user
            
            if (!emptyArray($request->roles)) {
                $adminEmployee->roles()->attach($request->roles);
            }
        }

        $employee = $company->employee()->create($dataEmployee);

        // Process inputing profile
        $dataProfile = $request->all();
        $employee = Employee::find($employee->id);

        $employee->profile()->create($dataProfile);

        return response()->json([
            'status' => 'success', 
            'message' => 'Data Karyawan Berhasil Ditambahkan',
            'data' => [
                'employee' => $employee,
                'profile' => $employee->profile,
            ]
        ], 200);
    }

    // /**
    //  *  Route ini hanya utk testing
    //  */
    // public function destroy(Employee $employee)
    // {
    //     try {
    //         if ($employee->delete()) {
    //             return response()->json([
    //                 'status' => 'success',
    //                 'message' => 'Data Telah Dihapus'
    //             ]);
    //         }
    //     } catch (\Throwable $th) {
    //         throw $th->getMessage();
    //     }
        
    // }
    protected function validator(Request $request)
    {
        

        return dd('ok');
    }
}
