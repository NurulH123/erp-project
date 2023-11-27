<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sending;

class SendingController extends Controller
{
    public function indexSender()
    {

        $data = Sending::OrderBy('id', 'desc')->get();

        return response()->json(['message' => 'role berhasil ditambahkan', 'data' => $data], 200);
    }

    public function oneSender($id)
    {

        $data = Sending::where('id', $id);

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function createSender(Request $request)
    {
        $data['sender'] = $request->sender;
        $pengiriman = Sending::create($data);

        return response()->json(['message' => 'pengiriman berhasil ditambahkan'], 200);
    }

    public function updateSender(Request $request, $id)
    {

        $data = Sending::where('id', $id)->first();
        $data['sender'] = $request->sender;
        $data->update();

        return response()->json(['message' => 'pengiriman berhasil diperbaharui'], 200);
    }

    public function deleteSender(Request $request, $id)
    {
        $pengiriman = Sending::destroy($id);

        return response()->json(['message' => 'pengiriman berhasil dihapus'], 200);
    }
}
