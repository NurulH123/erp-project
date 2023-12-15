<?php
namespace App\Repository\Services\Login;

use App\Models\User;
use App\Models\Role;
use App\Repository\Auth\LoginRepository;

class AuthService implements LoginRepository
{
    public function login($request)
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

        $token = auth()->user()->createToken()->plainTextToken;

        return response()->json(['user' => auth()->user(), 'token' => $token, 'role' => $role], 200);
    }
}