<?php

namespace App\Http\Controllers\Company;

use App\Models\Position;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;

class PositionController extends Controller
{
    public function index()
    {
        $sort = request('sort') ?? '5';

        $user = auth()->user()->employee;
        $companyId = $user->company->id;
        $positions = Position::whereHas('positionable', function(Builder $query) use($companyId){
                            $query->where('positionable_id', $companyId);
                        })
                        ->where('status', true)
                        ->paginate($sort);

        return response()->json([
            'status' => 'success', 
            'data' => $positions
        ]);
    }

    public function allData()
    {
        $positions = Position::all();

        return response()->json([
            'status' => 'success',
            'data' => auth()->user()->employee->company->positions
        ]);
    }

    public function show(Position $position)
    {
        return response()->json([
            'status' => 'success',
            'data' => $position
        ]);
    }

    public function create(Request $request)
    {
        $user = auth()->user()->employee;

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'code' => 'required',
            ],[
                'name.required' => 'Nama Harus Diisi',
                'code.required' => 'Kode Harus Diisi',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $validator->errors(),
                ], 442);
            }

            $data = $user->company->positions()->create($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Data Berhasil Ditambahkan',
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function update(Request $request, Position $position)
    {
        $user = auth()->user()->employee;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'code' => 'required',
        ],[
            'name.required' => 'Nama Harus Diisi',
            'code.required' => 'Kode Harus Diisi',
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors(),
            ]);
        }

        $user->company->positions()->update($request->all());
        $position = Position::findOrFail($position->id);

        return response()->json([
            'status'  => 'success',
            'message'   => 'Data Telah Diupdate',
            'position' => $position
        ], 200);
    }

    public function changeStatus(Position $position)
    {
        $status = !$position->status;
        $statusText = $status ? 'Diaktifkan' :  'Dinonaktifkan';
        $position->update(['status' => $status]);

        $updPosition = Position::findOrFail($position->id);

        return response()->json([
            'status' => 'success',
            'is_status' => $status,
            'message' => 'Posisi '.$position->name. ' Telah '.$statusText,
            'position' => $updPosition
        ]);
    }
}
