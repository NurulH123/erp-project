<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        if (User::where("email", $request->email)->count() > 0) {
            return response()->json(["message" => "Email sudah digunakan"], 403);
        } else {
            try {
                $validator =  Validator::make(
                    $request->all(),
                    [
                        'username' => 'required|max:255',
                        'email' => 'required|email|unique:users,email',
                        'password' => 'required|confirmed',
                        // 'gender' => 'required|in:Laki-laki,Perempuan',
                        // 'phone' => 'required|unique:profiles',
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
            } catch (\Throwable $th) {
                return $th->getMessage();
            }
            $data = $validator->getData();
            $data['password'] = bcrypt($request->password);
            
            $user = User::create($data);

            // create code
            $number = 1000000 + $user->id;
            $uniqCode = (string)substr($number, 1);

            $code = '00-'.date("Ymd").'-'.$uniqCode;
            
            // Tambah Admin Owner
            $user->adminEmployee()->create(['code' => $code]);

            $token = $user->createToken('api')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil menambahkan akun', 
                'user' => $user, 
                'token' => $token
            ], 200);
        }

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
            return response(['message' => 'Email belum terdaftar'], 404);
        }

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors(),
            ], 442);
        }

        if (!auth()->attempt($validator->getData())) {
            return response(['message' => 'password atau email yang anda masukkan tidak sesuai'], 404);
        }

        $user = User::where('email', $request->email)
                ->first(['id', 'username', 'email', 'status', 'is_owner']);

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

        $data = $this->checkLoginEmployee($user);

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

        $data = collect($user)->toArray();
        $data['company'] = $company;

        return $data;
    }
}
