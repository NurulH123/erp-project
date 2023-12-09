<?php

namespace App\Http\Controllers\Company;

use App\Models\Position;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PositionRequest;

class PositionController extends Controller
{
    public function index()
    {
        return Position::all();
    }

    public function create(PositionRequest $request)
    {
        $data = $request->validate();

        if (Position::create($data)) {
            return response()->json(['message' => 'Data Telah Ditambahkan'], 200);
        }
    }

    public function update(Request $request, Position $position)
    {
        $data['name'] = $request->name;
        $data['code'] = $request->code;

        $position->update($data);
        $updPosition = Position::findOrFail($position->id);

        return response()->json([
            'message'   => 'Data Telah Diupdate',
            'position' => $updPosition
        ], 200);
    }

    public function changeStatus(Position $position)
    {
        $status = !$position->status;
        $position->update(['status' => $status]);

        $updPosition = Position::findOrFail($position->id);

        return response()->json([
            'message' => 'Status Berhasil Diubah',
            'position' => $updPosition
        ]);
    }
}
