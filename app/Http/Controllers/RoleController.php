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
        $user = auth()->user();
        $roles = $user->company->roles;

        return response()->json([
            'status' => 'success', 
            'data' => $roles
        ]);
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),['name' => 'required'],['name.required' => 'Nama Harus Diisi']);
    
        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => $validator->errors()], 442);
        }

        $data = $request->all();
        $data['caption'] = Str::snake($request->name);

        $role = $user->company->roles()->create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Role berhasil ditambahkan', 
            'data' => $role
        ]);
    }

    public function update(Request $request,Role $role)
    {
        $validator = Validator::make($request->all(),['name' => 'required'],['name.required' => 'Nama Harus Diisi']);
    
        if ($validator->fails()) {
            return response()->json(['status' => 'faiiled', 'message' => $validator->errors()], 442);
        }

        $data = $request->all();
        $data['caption'] = Str::snake($request->name);

        $role->update($data);
        $role = Role::findOrFail($role->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Role berhasil diperbaharui', 
            'data' => $role
        ]);

    }

    public function changeStatus(Role $role)
    {
        $status = !$role->status;
        $statusText = $status ? 'aktifkan' :  'non aktifkan';
        $role->update(['status' => $status]);

        $role = Role::find($role->id);

        return response()->json([
            'status' => 'success',
            'is_status' => $status,
            'message' => 'Role berhasil di '.$statusText,
            'data' => $role
        ]);
    }
}
