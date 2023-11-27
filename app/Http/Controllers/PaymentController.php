<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function indexPayment()
    {

        $data = Payment::OrderBy('id', 'desc')->get();

        return response()->json(['message' => 'role berhasil ditambahkan', 'data' => $data], 200);
    }

    public function onePayment($id)
    {

        $data = Payment::where('id', $id)->first();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function createPayment(Request $request)
    {
        $data['method'] = $request->method;
        $payment = Payment::create($data);

        return response()->json(['message' => 'payment berhasil ditambahkan', 'data' => $payment], 200);
    }

    public function updatePayment(Request $request, $id)
    {

        $data = Payment::where('id', $id)->first();
        $data['method'] = $request->method;
        $data->update();

        return response()->json(['message' => 'payment berhasil diperbaharui', 'data' => $data], 200);
    }

    public function deletePayment(Request $request, $id)
    {
        $payment = Payment::destroy($id);

        return response()->json(['message' => 'payment berhasil dihapus'], 200);
    }
}
