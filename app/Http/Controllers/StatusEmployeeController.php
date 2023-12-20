<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StatusEmployee;
use Illuminate\Database\Console\Migrations\StatusCommand;
use Illuminate\Support\Facades\Validator;

class StatusEmployeeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $empoyeeStatus = $user->company->employeeStatus;

        return response()->json([
            'status' => 'success',
            'data' => $empoyeeStatus,
        ]);
    }

    public function create(Request $request)
    {
        $user  = auth()->user();

        $validator = Validator::make($request->all(), [
            'name' =>  'required',
            'code' => 'required',
        ], [
            'name.requiired' => 'Nama Harus Diisi',
            'code.required' => 'Kode Harus Diisi'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => $validator->errors()], 442);
        }

        $data = $validator->getData();

        $status = $user->company->employeeStatus()->create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Telah Ditambahkan',
            'data' => $status,
        ]);
    }

    public function update(Request $request, StatusEmployee $status)
    {
        $data = $request->all();

        $status->update($data);
        $status = StatusEmployee::find($status->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Telah Ditambahkan',
            'data' => $status,
        ]);
    }

    public function changeStatus(StatusEmployee $employeeStatus)
    {
        $status = !$employeeStatus->status;
        $statusText = $status ? 'Aktif' : 'Non Aktif';

        $employeeStatus->update(['status' => $status]);

        return response()->json([
            'status' => 'success',
            'is_status' => $status,
            'message' => 'Status '.$employeeStatus->name.' '.$statusText
        ]);
    }
}
