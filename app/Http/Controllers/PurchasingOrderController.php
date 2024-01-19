<?php

namespace App\Http\Controllers;

use App\Models\DetailPurchasingOrder;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\PurchasingOrder;
use Illuminate\Support\Facades\Validator;

class PurchasingOrderController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $transactionPo = $user->company->transactionPo;

        return response()->json([
            'status' => 'success',
            'data' => $transactionPo
        ]);
    }

    public function show($id)
    {
        $purchaseOrder = PurchasingOrder::with('details')->find($id);

        return response()->json([
            'status' => 'success',
            'data' => $purchaseOrder
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['po'] = $request->po ?? '';
        $data['detail_po'] = $request->detail_po ?? '';

        $validator = Validator::make($data, [
                'po' => 'required',
                'detail_po' => 'required',
            ],[
                'po.required' => 'PO Harus Diisi',
                'detail_po.required' => 'Detail PO Harus Diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'required',
                'message' => $validator->errors()
            ]);
        }

        $dataPo = $data['po'];
        $detailPO = collect($data['detail_po']);
        
        $warehouse = Warehouse::find($dataPo['warehouse_id']);

        $user = auth()->user();
        $company = $user->company;
        $dataPo['code_employee'] = $user->adminEmployee->code;
        $dataPo['code_transaction'] = date('YmdHis');
        $dataPo['date_transaction'] = date('Y-m-d', strtotime($dataPo['date_transaction']));

        // Create transaksi po
        $transactionPo = $company->transactionPo()->create($dataPo);

        // Create detail transaksi  po
        $newDetail = [];
        foreach ($detailPO as $detail) {
            $createDetail = $transactionPo->details()->create($detail);
            $dataDetail = collect($createDetail)
                            ->only('id', 'product_id', 'order')
                            ->toArray();

            array_push($newDetail, $dataDetail);
        }

        $data = $transactionPo->toArray();
        $data['details'] = $newDetail;

        return response()->json([
            'status' => 'success',
            'message' => 'Data Transaksi PO Berhasil Ditambahkan',
            'data' => $data
        ]);
    }

    public function destroy(PurchasingOrder $purchase)
    {
        $purchase->delete();
        $purchase->invoice()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data Transaksi PO Telah Dihapus'
        ]);
    }
}
