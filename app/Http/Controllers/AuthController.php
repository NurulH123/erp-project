<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    protected $units = 0;
    protected $vendor = 0;
    protected $role = 0;
    protected $position = 0;
    protected $category = 0;
    protected $status_employee = 0;
    protected $customer = 0;

    public function register(Request $request)
    {
        $validator =  Validator::make(
            $request->all(),
            [
                'username' => 'required|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed',
            ],
            [
                'username.required' => 'Nama Harus Diisi',
                'email.required' => 'Email Harus Diisi',
                'email.email' => 'Format Email Tidak Sesuai',
                'email.unique' => 'Email Sudah Digunakan',
                'password.required' => 'Password Harus Diisi',
                'password.confirmed' => 'Konfirmasi Password Tidak Tepat',
            ]
        ); 
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ], 442);
        }

        $data = $validator->getData();
        $data['password'] = bcrypt($request->password);
        
        $user = User::create($data);

        // create code
        $number = 1000000 + $user->id;
        $uniqCode = (string)substr($number, 1);

        $code = '00-'.date("Ymd").'-'.$uniqCode;
        
        // Tambah Admin Employee
        $user->adminEmployee()->create(['code' => $code]);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil menambahkan akun',
            'user' => $user,
            'token' => $token
        ], 200);

    }


    public function login(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'email'     => 'required',
            'password'  => 'required',
        ], [
            'email.required'    => 'Email Harus Diisi',
            'email.unique'      => 'Email Sudah Terdaftar',
            'password.required' => 'Password Harus Diisi',
        ]);

        if (User::where('email', $request->email)->first() == null) {
            return response()->json([
                'message' => 'Email belum terdaftar'
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors(),
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        if (!auth()->attempt($validator->getData())) {
            return response()->json([
                'message' => 'password atau email yang anda masukkan tidak sesuai'
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        $user = User::where('email', $request->email)
                ->first(['id', 'username', 'email', 'status', 'is_owner']);

        $profile = null;
        $employee = $user->adminEmployee->employee;

        // Validasi user active
        if (!$user->status) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Akun Anda Tidak aktif'
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!is_null($employee)) {
            $profile = $user->adminEmployee->employee->profile;
        }
        
        $user->profile = $profile;

        $data = $this->checkLoginEmployee($user);

        $token = auth()->user()->createToken('api')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'token' => $token
        ], 200);
    }

    public function me()
    {
        $user = User::where('id',  auth()->id())
                ->first(['id', 'username', 'email', 'status', 'is_owner']);

        $profile = null;
        $employee = $user->adminEmployee->employee;
        $dataMaster = $this->dataMaster();
        $user->employee = collect($employee)->only('id', 'code', 'status');

        if (!is_null($employee)) {
            $profile = $user->adminEmployee->employee->profile;
        }

        $user->profile = $profile;
        
        $collRoles = collect($user->adminEmployee->roles)->pluck('id', 'name')->toArray();
        $idRoles = array_values($collRoles);
        
        $roles = Role::with('permission:id,name,caption,status')
                    ->whereIn('id', $idRoles)
                    ->get(['id','name','caption','code','status']);

        $data = $this->checkLoginEmployee($user);
        $data['role'] = $roles;
        $data['master_data'] = $dataMaster;

        return response()->json(['data' => $data]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(["status" => "success", 'message' => 'Logged Out'], 200);
    }

    private function checkLoginEmployee($user)
    {
        $company = is_null($user->company) ? 
                    null : collect($user->company)->toArray();
                    
        if (!$user['is_owner']) {
            $code = $user->adminEmployee->code;
            $employee = Employee::where('code', $code)->first();
            $company = collect($employee->company)->toArray();
        }

        $data = collect($user)->except('admin_employee')->toArray();
        $data['company'] = $company;

        return $data;
    }

    private function dataMaster()
    {
        $checkEmployee = is_null(auth()->user()->employee);
        $user = $checkEmployee ? 
                    auth()->user() : auth()->user()->employee;
        $company = $user->company;

        return $checkEmployee ? null : [
            'unit' => !is_null($company->units) ? count($company->units) : 0,
            'vendor' =>  !is_null($company->vendor) ? count($company->vendor) : 0,
            'role' =>  !is_null($company->roles) ? count($company->roles) : 0,
            'position' =>  !is_null($company->positions) ? count($company->positions) : 0,
            'category' =>  !is_null($company->productCategories) ? count($company->productCategories) : 0,
            'warehouse' =>  !is_null($company->warehouses) ? count($company->warehouses) : 0,
            'status_employee' =>  !is_null($company->employeeStatus) ? count($company->employeeStatus) : 0,
            'customer' =>  !is_null($company->customers) ? count($company->customers) : 0
        ];

    }
}
