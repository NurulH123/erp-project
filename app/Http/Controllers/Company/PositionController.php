<?php

namespace App\Http\Controllers\Company;

use App\Models\Position;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PositionRequest;
use App\Models\Company;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    private $user, $company;

    public function __construct()
    {
        $this->user = auth()->user();
        $this->company = Company::find($this->user->company->id);
    }

    public function index()
    {
        $positions = $this->company->positions;

        return response()->json(['status' => 'success', 'data' => $positions]);
    }

    public function create(Request $request)
    {
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
                    'message' => 'Terjadi Kesalahan',
                    'data' => $validator->errors(),
                ]);
            }

            $data = $this->company->positions()->create($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Data Berhasil Ditambahkan',
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            throw $th->getMessage();
        }
    }

    public function update(Request $request, Position $position)
    {
        $data['name'] = $request->name;
        $data['code'] = $request->code;

        $this->company->position->update($data);
        $position = Position::findOrFail($position->id);

        return response()->json([
            'message'   => 'Data Telah Diupdate',
            'position' => $position
        ], 200);
    }

    public function changeStatus(Position $position)
    {
        $status = !$position->status;
        $this->company->position->update(['status' => $status]);

        $updPosition = Position::findOrFail($position->id);

        return response()->json([
            'message' => 'Status Berhasil Diubah',
            'position' => $updPosition
        ]);
    }
}
