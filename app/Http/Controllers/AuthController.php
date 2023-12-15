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

                        'email' => 'required|email|unique:users',
                        'password' => 'required|confirmed',
                        'first_name' => 'required|max:255',
                        'last_name' => 'required|max:255',
                        'role_id' => 'required',
                    ]

                ); 

                if ($validator->fails()) {
                    return response()->json(
                        [
                        'status' => 'Terjadi kesalahan',
                        'mmessage' => $validator->errors()
                        ]
                    );
                }
            } catch (\Throwable $th) {
                return $th->getMessage();
            }
            $data = $validator->getData();

            $data['password'] = bcrypt($request->password);
            $number = 00000;

            $user = User::create($data);
            $code = (int)date("Ymd$number") + $user->id;
            
            // Tambah Admin Owner
            $user->adminEmployee()->create(['code' => $code]);

            $token = $user->createToken('api')->plainTextToken;

            return response()->json(['message' => 'berhasil menambahkan akun', 'user' => $user, 'token' => $token], 200);
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
            return response(['error_message' => 'Password yang anda masukkan tidak sesuai'], 404);
        }


        $user = User::where('email', $request->email)->first();
        $role = Role::where('id', $user->role_id)->first()->name;

        $token = auth()->user()->createToken('api')->plainTextToken;

        return response()->json(['user' => auth()->user(), 'token' => $token, 'role' => $role], 200);


    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(["status" => "Logged Out"], 200);
    }
}
