<?php

namespace App\Http\Controllers;

use App\Models\AdminEmployee;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminRoleController extends Controller
{
    /**
     *  User hanya bisa menambahkan role, tidak bisa menghapus atau update.
     *  Hal ini karena sebagai history atau rekam jejak dari karyawan
     */
    public function addRoleToAdmin(Request $request, Employee $employee)
    {
        $admin = AdminEmployee::where('code', $employee->code)->first();
        
        if (!$employee->is_admin) return response()->json(['status' => 'failed', 'message' => 'Maaf, '.$employee->username.' Bukan Admin'],442);
        $validator = Validator::make($request->all(), ['role_id' => 'required'], ['role_id.required' => 'Permission Harus Diisi']);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ], 442);
        }

        $admin->roles()->attach($request->role_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Role Berhaasil Ditambahkan'
        ]);
    }

    public function changeStatus(Request $request, Employee $employee)
    {
        $admin = AdminEmployee::where('code', $employee->code)->first();

        $validator = Validator::make($request->all(), ['role_id' => 'required'], ['role_id.required' => 'Role Harus Diisi']);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ], 442);
        }

        $role = $admin->roles->find($request->role_id);
        $status = !$role->pivot->status;
        $statusText = $status ? 'Mengaktifkan' : 'Menonaktifkan';

        $admin->roles()->updateExistingPivot($request->role_id, ['status' => $status]);

        return response()->json([
            'status' => 'success',
            'message' => $employee->username.' '.$statusText.' Role '.$role->name,
        ]);
    }
}
