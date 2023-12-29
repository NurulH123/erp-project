<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionRoleController extends Controller
{
    public function givePermissionTo(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), ['permission_id' => 'required'], ['permission_id.required' => 'Permission Harus Diisi']);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ], 442);
        }

        $role->permission()->attach($request->permission_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Berhasil Ditambahkan'
        ]);
    }

    public function updateRolePermission(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), ['permission_id' => 'required'], ['permission_id.required' => 'Permission Harus Diisi']);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ], 442);
        }

        $role->permission()->sync($request->permission_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Berhasil Diubah'
        ]);
    }
}
