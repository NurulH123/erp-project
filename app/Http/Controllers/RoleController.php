<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Role;

class RoleController extends Controller
{
    public function indexRole()
    {

        $data = Role::OrderBy('id', 'desc')->get();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function oneRole($id)
    {

        $data = Role::where('id', $id)->first();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function createRole(Request $request)
    {

        $data['name'] = $request->name;
        $role = Role::create($data);

        return response()->json(['message' => 'role berhasil ditambahkan', 'data' => $role], 200);
    }

    public function updateRole(Request $request, $id)
    {

        $data = Role::where('id', $id)->first();
        $data['name'] = $request->name;
        $data->update();

        return response()->json(['message' => 'role berhasil diperbaharui', 'data' => $data], 200);
    }

    public function deleteRole(Request $request, $id)
    {
        $role = Role::destroy($id);

        return response()->json(['message' => 'role berhasil dihapus'], 200);
    }
}
