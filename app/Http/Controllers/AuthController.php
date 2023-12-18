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
                        'email' => 'required|email|unique:users',
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
                    ], 500);
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
                'message' => 'berhasil menambahkan akun', 
                'user' => $user, 'token' => $token
            ], 200);
        }

    }


    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (User::where('email', $request->email)->first() == null) {
            return response(['error_message' => 'Email belum terdaftar'], 404);
        }

        if (!auth()->attempt($data)) {
            return response(['message' => 'password atau email yang anda masukkan tidak sesuai'], 404);
        }

        $user = User::where('email', $request->email)->first();

        $token = auth()->user()->createToken('api')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'user' => auth()->user(), 
            'token' => $token
        ], 200);


    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(["status" => "Logged Out"], 200);
    }
}
