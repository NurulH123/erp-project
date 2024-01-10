<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PurchasingOrderController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required',
            'date_transaction' => 'required',
            'detail_po' => 'required'
        ], [
            'vendor_id.required' => 'Nama Vendor Harus Diisi',
            'date_transaction.required' => 'Tanggal Transaksi Harus Diisi',
            'detail_po.required' => 'Detail Transaksi Harus Diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'required',
                'message' => $validator->errors()
            ]);
        }

        $dataTransaction = $request->only('vendor_id', 'date_transaction');
        $dataTransaction['code_transaction'] = $user->id.'-'.date('YmdHis');
        $dataTransaction['code_employee'] = $user->adminEmployee->code;

        // Penambahan transaksi produk
        $transactionPo = $company->transactionPo()->create($dataTransaction); 

        // Penambahan detail dari transaction product
        $request->detail_po->each(function($item) use($transactionPo) {
            $transactionPo->details()->create($item);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data Berhasil Ditambahkan'
        ]);
    }
}
