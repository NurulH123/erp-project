<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ProfileController extends Controller
{
    public function indexProfile(Request $request)
    {
        $user = $request->user();
        $profile = User::where('code', $user->code)->with('role')->orderBy('id', 'desc')->first();

        return response()->json(['message' => 'data berhasil diambil', 'data' => $profile], 200);
    }


    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $profile = User::where('code', $user->code)->first();

        if ($profile->email != $request->email) {
            $data = $request->validate([
                'email' => 'required|email|unique:users',
            ]);
        }

        if ($request->file('image')) {
            $data = $request->validate([
                'username' => 'required|max:255',
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'role_id' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',

            ]);

            if ($request->file('image')->isValid()) {

                $photoPath = $request->file('image')->store('user_photos', 'public');

                $data['image'] = $photoPath;
            }
        } elseif ($request->image) {
            $data = $request->validate([
                'username' => 'required|max:255',
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'role_id' => 'required',
                'image' => 'required',

            ]);

        } elseif ($request->image == null) {
            $data = $request->validate([
                'username' => 'required|max:255',
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'role_id' => 'required',
            ]);

            $data['image'] = null;
        }

        if ($request->password) {
            // $pass = $request->validate([
            //     'password' => 'required|confirmed',
            // ]);

            $data['password'] = bcrypt($request->password);
        }

        if ($request->status) {
            $data['status'] = $request->status;
        }

        $profile->update($data);

        return response()->json(['message' => 'data berhasil diperbaharui', 'data' => $profile], 200);

    }

    public function oneProfile(Request $request, $id)
    {

        $profile = User::where('id', $id)->with('role')->first();

        return response()->json(['message' => 'data berhasil diambil', 'data' => $profile], 200);
    }

    public function updateUser(Request $request, $id)
    {

        $profile = User::where('id', $id)->first();

        if ($profile->email != $request->email) {
            $data = $request->validate([
                'email' => 'required|email|unique:users',
            ]);
        }

        if ($request->file('image')) {
            $data = $request->validate([
                'username' => 'required|max:255',
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'role_id' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',

            ]);

            if ($request->file('image')->isValid()) {

                $photoPath = $request->file('image')->store('user_photos', 'public');

                $data['image'] = $photoPath;
            }
        } elseif ($request->image) {
            $data = $request->validate([
                'username' => 'required|max:255',
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'role_id' => 'required',
                'image' => 'required',

            ]);

        } elseif ($request->image == null) {
            $data = $request->validate([
                'username' => 'required|max:255',
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'role_id' => 'required',
            ]);

            $data['image'] = null;
        }

        if ($request->password) {

            $data['password'] = bcrypt($request->password);
        }

        if ($request->status) {
            $data['status'] = $request->status;
        }

        $profile->update($data);

        return response()->json(['message' => 'data berhasil diperbaharui', 'data' => $profile], 200);

    }


    public function allProfile()
    {

        $user = User::with('role')->orderBy('id', 'desc')->get();

        return response()->json(['data' => $user]);
    }

    public function deleteProfile(Request $request, $id)
    {

        try {
            User::destroy($id);

            return response()->json(['message' => 'user berhasil dihapus'], 200);

        } catch (\Throwable $e) {

            return response()->json(['message' => 'user memiliki order'], 500);
        }

    }

}
