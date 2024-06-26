<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\StatusEmployee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class StatusEmployeeController extends Controller
{
    public function index()
    {
        $sort = request('sort') ?? '5';
        $search = request('search') ?? '';

        $empoyeeStatus = StatusEmployee::whereHas('statusable', function(Builder $query) {
            $user = auth()->user()->employee;
            $companyId = $user->company->id;

            $query->where('id', $companyId);
        })
        ->where('name', 'like', "%$search%")
        ->paginate($sort);

        return response()->json([
            'status' => 'success',
            'data' => $empoyeeStatus,
        ]);
    }

    public function allData() 
    {
        $user = auth()->user()->employee;
        $status = $user->company->employeeStatus->where('status', true);

        return response()->json([
            'status' => 'success',
            'data' => $status
        ]);
    }

    public function show(StatusEmployee $status)
    {
        return response()->json([
            'status' => 'success',
            'data' => $status
        ]);
    }

    public function create(Request $request)
    {
        $user  = auth()->user()->employee;

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
