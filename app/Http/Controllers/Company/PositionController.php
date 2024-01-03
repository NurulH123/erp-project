<?php

namespace App\Http\Controllers\Company;

use App\Models\Position;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $positions = $user->company->positions;

        return response()->json([
            'status' => 'success', 
            'data' => $positions
        ]);
    }

    public function create(Request $request)
    {
        $user = auth()->user();

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
        $user = auth()->user();
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
        $position->update(['status' => $status]);

        $updPosition = Position::findOrFail($position->id);

        return response()->json([
            'status' => 'success',
            'is_status' => $status,
            'message' => 'Status Berhasil Diubah',
            'position' => $updPosition
        ]);
    }
}
