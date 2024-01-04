<?php

namespace App\Http\Controllers\Company;

use App\Models\User;
use App\Models\Company;
use App\Models\Position;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\AdminEmployee;
use App\Http\Controllers\Controller;
use App\Models\ProfileEmployee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function index()
    {
        $user = User::find(Auth::id());
        $pluck = collect($user->company->employee)->pluck('id', 'username')->toArray();
        $IdEmployee = array_values($pluck);
        $employees = Employee::with('profile')
                        ->whereIn('id', $IdEmployee)
                        ->get(['id', 'code', 'username', 'email', 'password', 'is_admin', 'status']);

        return response()->json([
            'status' => 'success', 
            'data' => $employees,
        ], 200);
    }

    public function show($id)
    {
        $employee = Employee::with('profile')
                        ->where('id', $id)
                        ->first(['id', 'code', 'username', 'email', 'is_admin', 'status']);
    
                        // dd($employee, $id);
        return response()->json([
            'status' => 'success',
            'data' => $employee,
        ]);
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
            'email'     => 'required|email|unique:employees,email',
            'email'     => 'required|email|unique:users,email',
            'is_admin'  => 'required|in:0,1',
            'gender'    => 'required',
            'phone'     => 'required|unique:profile_employees,phone',
            'address'   => 'required',
        ];

        $messages = [
            'username.required' => 'Nama Harus Diisi',
            'is_admin.required' => 'Is Admin Harus Diisi',
            'is_admin.in'       => 'Is Admin Harus Bernilai 1 atau 0',
            'email.required'    => 'Email Harus Diisi',
            'email.unique'      => 'Maaf, Email Sudah Terdaftar',
            'email.email'       => 'Format Email Tidak Sesuai',
            'gender.required'   => 'Jenis Kelamin Harus Diisi',
            'phone.required'    => 'Hp Harus Diisi',
            'phone.unique'      => 'Maaf, Nomor Hp Sudah Terdaftar',
            'address.required'  => 'Alamat Harus Diisi',
        ];

        $position = Position::find($request->position_id);
        $statusEmployee = Position::find($request->status_employee_id);

        if (is_null($position) || is_null($statusEmployee)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Terdapat Kesalahan Posisi atau Status Karyawan',
            ], 442);
        }

        if ($request->is_admin) {
            // Rules user register
            $rules['password'] = 'required|confirmed';

            // Message user register
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
        $dataEmployee = $request->only('username', 'email', 'is_admin');
        $dataEmployee['code'] = $code;

        $dataProfile = $request->all();
        $dataProfile['join'] = isset($request->join) ? date('Y-m-d', strtotime($request->join)) : null;
        $dataProfile['resaign'] = isset($request->resaign) ? date('Y-m-d', strtotime($request->resaign)) : null;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = date('Ymd').'.'.$file->getClientOriginalExtension();
            $file->move('uploads/photo/profile', $filename);

            $dataProfile['photo'] = $filename;
        }
        
        // Process inputing data employee
        if ($request->is_admin) {
            $dataUser = $request->only('username', 'email', 'password');
            
            $dataUser['is_owner'] = false;
            $dataUser['password'] = bcrypt($request->password);
            $dataEmployee['password'] = $request->password;

            $employee = $company->employee()->create($dataEmployee);
            $newUser = User::create($dataUser); // Menambahkan data us er
            $adminEmployee = $newUser->adminEmployee()->create(['code' => $code]);// Menambahkan data user
            
            if (!empty($request->roles)) {
                $adminEmployee->roles()->attach($request->roles);
            }
        } else {
            $employee = $company->employee()->create($dataEmployee);
        }

        // Process inputing profile
        $employee->profile()->create($dataProfile);

        return response()->json([
            'status' => 'success', 
            'message' => 'Data Karyawan Berhasil Ditambahkan',
            'data' => $employee,
        ], 200);
    }

    public function update(Request  $request, Employee $employee)
    {
        // Prosess Validasi
        $rules =  [
            'username'  => 'required',
            'email'     => 'required',
        ];

        $messages = [
            'username.required' => 'Nama Harus Diisi',
            'email.required'    => 'Email Harus Diisi',
            'email.email'       => 'Format Email Tidak Sesuai',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ]);
        }

        // Disini tidak bisa mengupdate is_admin
        // is_admin hanya bisa diupdate melalui method changeAdmin()  
        $dataEmployee = $request->only('username', 'email'); 

        // Process inputing profile
        $dataProfile = $request->except('username', 'is_admin', 'email');
        $dataProfile['join'] = isset($request->join) ? date('Y-m-d', strtotime($request->join)) : null;
        $dataProfile['resaign'] = isset($request->resaign) ? date('Y-m-d', strtotime($request->resaign)) : null;
        
        if ($request->hasFile('photo')) {
            $photo = $employee->profile->photo;
            
            if (!is_null($photo)) {
                unlink('uploads/photo/profile'.$photo);
            }

            $file = $request->file('photo');
            $filename = date('Ymd').'.'.$file->getClientOriginalExtension();
            $file->move('uploads/photo/profile', $filename);

            $dataProfile['photo'] = 'uploads/photo/profile/'.$filename;
        }

        $employee->update($dataEmployee);
        $employee->profile()->update($dataProfile);

        if ($employee->is_admin) {
            $admin = AdminEmployee::where('code', $employee->code)->first();
            $admin->user()->update($dataEmployee);
        }

        // dd('keluar');
        $updEmployee = Employee::with('profile')
                        ->where('id', $employee->id)
                        ->first(['id', 'code', 'username', 'email', 'is_admin', 'status']);

        return response()->json([
            'status' => 'success', 
            'message' => 'Data Karyawan Berhasil Diubah',
            'data' => $updEmployee
        ], 200);
    }

    public function changeAdmin(Request $request, Employee $employee)
    {
        $user = auth()->user();
        $isAdmin = !$employee->is_admin;
        $adminText = $isAdmin ? 'Admin' : 'Bukan Admin';

        // Process inputing data employee
        if ($isAdmin) {
            $validator = Validator::make($request->all(), 
                    ['password' => 'required|confirmed'],
                    [
                        'password.required' =>  'Password Harus Diisi',
                        'password.confirmed' =>  'Konfirmasi Password Tidak Sesuai',
                    ]
            );

            if ($validator->fails()) {
                return response()->json(['status' => 'failed', 'message' => $validator->errors()], 442);
            }

            $dataUser = [
                'username'  => $employee->username,
                'email'     => $employee->email,
                'is_owner'  => false,
                'password'  => bcrypt($request->password),
            ];

            $user = User::create($dataUser); // Menambahkan data user
            $adminEmployee = $user->adminEmployee()->create(['code' => $employee->code]);// Menambahkan data user

        } else {
            $adminEmployee = AdminEmployee::where('code', $employee->code)->first();   
            $adminEmployee->user()->delete();
            $adminEmployee->delete();
        }

        $employee->update(['is_admin' => $isAdmin]);

        return response()->json([
            'status' => 'success',
            'meessage' => 'Data Berhasil Diubah Menjadi '.$adminText,
        ]);
    }

    public function changeStatus(Employee $employee)
    {
        $status = !$employee->status;
        $statusText = $status ? 'Aktif' : 'Tidak Aktif';

        $employee->update(['status' => $status]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status '.$employee->username.' '.$statusText,
        ]);
    }
}
