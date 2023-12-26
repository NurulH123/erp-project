<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CompanyPermissionController extends Controller
{
    public function addPermissionTo(Request $request)
    {
        $validator = Validator::make($request->all(),
            ['permission_group_id' => 'required'], 
            ['permission_group_id.required' => 'Permission Harus Diisi']
        );
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors(),
            ], 442);
        }

        $user = auth()->user();
        $permissionGroup = $request->permission_group_id;

        $user->company->permission()->attach($permissionGroup);

        return response()->json([
            'status' => 'success',
            'message' => 'Permission Telah ditambahkan',
        ]);
    }

    public function updateCompanyPermission(Request $request)
    {
        $user = auth()->user();
        $permissionGroup = $request->permission_group_id;

        $user->company->permission()->sync($permissionGroup);
        $permission = $user->company->permission;

        return response()->json([
            'status' => 'success',
            'message' => 'Data Berhasil Diubah',
            'data' => [
                'status' => true,
                'company' => $user->company,
            ],
        ]);
    }
}
