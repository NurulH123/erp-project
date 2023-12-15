<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index()
    {
        $data = Role::OrderBy('id', 'desc')->get();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function show(Role $role)
    {
        return $role;
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),['name' => 'required'],['name.required' => 'Nama Harus Diisi']);
        
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()]);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        $data['name'] = $request->name;
        $data['caption'] = Str::slug($request->name);
        $role = Role::create($data);

        return response()->json(['message' => 'role berhasil ditambahkan', 'data' => $role], 200);
    }

    public function update(Request $request,Role $role)
    {

        $data['name'] = $request->name;
        $data['slug'] = Str::slug($request->name);

        $role->update($data);

        return response()->json(['message' => 'role berhasil diperbaharui', 'data' => $role], 200);
    }

    public function destroy(Role $role)
    {
        if (!$role->delete()) {
            return response()->json(['message' => 'Terjadi kesalahan. Coba cek lagi']);
        }

        return 'Data Berhasil Dihapus';
    }

    public function changeStatus(Role $role)
    {
        $role->update(['status' => false]);

        return response()->json(['message' => 'role berhasil di non aktifkan'], 200);
    }
}
