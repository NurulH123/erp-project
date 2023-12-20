<?php

namespace App\Http\Controllers\Auth;

use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all()
                        ->groupBy('permission_group_id');

        return response()->json([
            'data'  => $permissions
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),['name' => 'required'], ['name.required' => 'Nama Harus Diisi']);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => $validator->errors()], 442);
        }

        $data = $request->all();
        $data['caption'] = Str::snake($request->name);

        $permission = Permission::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Berhasil Ditambahkan',
            'data' => Permission::find($permission->id),
        ]);
    }

    public function update(Request $request, Permission $permission)
    {
        $validator = Validator::make($request->all(),['name' => 'required'], ['name.required' => 'Nama Harus Diisi']);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => $validator->errors()], 442);
        }

        $data = $request->all();
        $data['caption'] = Str::snake($request->name);

        $permission->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Berhasil Diubah',
            'data' => $permission
        ]);
    }

    public function changeStatus(Permission $permission)
    {
        $status = !$permission->status;
        $statusText = $status ? 'Aktif' : 'Non Aktif';

        $permission->update(['status' => $status]);

        return response()->json([
            'status' => 'success',
            'is_status'  => $status,
            'message' => 'Status Permissioin '.$statusText
        ]);
    }
}
