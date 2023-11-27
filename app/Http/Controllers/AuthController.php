<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;


class AuthController extends Controller
{
    public function register(Request $request)
    {

        if (User::where("email", $request->email)->count() > 0) {
            return response()->json(["message" => "Email sudah digunakan"], 403);
        } else {

            $data = $request->validate([
                'username' => 'required|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed',
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'role_id' => 'required',

            ]);


            $data['password'] = bcrypt($request->password);
            $number = mt_rand(1000, 9999);
            $code = date("Ymd$number");
            $data['code'] = $code;

            $user = User::create($data);

            $token = $user->createToken('API Token')->accessToken;

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
        $token = auth()->user()->createToken('API Token')->accessToken;


        return response()->json(['user' => auth()->user(), 'token' => $token, 'role' => $role], 200);


    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(["status" => "Logged Out"], 200);
    }
}
