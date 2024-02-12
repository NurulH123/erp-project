<?php

namespace App\Http\Controllers;

use App\Models\DetailPurchasingOrder;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\PurchasingOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class PurchasingOrderController extends Controller
{
    public function index()
    {
        $sort = request('sort') ?? 5;

        $user = auth()->user();
        $companyId = $user->company->id;
        $purchaseOrders = PurchasingOrder::whereHas('company', function(Builder $query) use($companyId){
                            $query->where('id', $companyId);
                        })->paginate($sort);

        return response()->json([
            'status' => 'success',
            'data' => $purchaseOrders
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

        if (!isset($request->po) || !isset($request->detail_po)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Data Purchase Order atau Detail Tidak  Boleh Kosong'
            ]);
        }

        $dataPo = $request->po;
        $validator = Validator::make($dataPo, [
                'vendor_id' => 'required',
                'date_order' => 'required',
                'warehouse_id' => 'required',
            ],[
                'vendor_id.required' => 'Vendor Masih Kosong',
                'date_order.required' => 'Tanggal Transaksi Masih Kosong',
                'warehouse_id.required' => 'Gudang Penyimpanan Belum Diisi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'required',
                'message' => $validator->errors()
            ]);
        }

        $detailPO = collect($request->detail_po);
        
        // $warehouse = Warehouse::find($dataPo['warehouse_id']);

        // Data transaksi
        $user = auth()->user();
        $company = $user->company;
        $dataPo['code_employee'] = $user->adminEmployee->code;
        $dataPo['code_transaction'] = date('YmdHis');
        $dataPo['date_order'] = date('Y-m-d', strtotime($dataPo['date_order']));

        // Create transaksi po
        $transaction = $company->transactionPo()->create($dataPo);

        // Create detail transaksi  po
        $detailPO->each(function($item) use($transaction){
            $transaction->details()->create($item);
        });

        $newData = $transaction;
        $newData['details'] = $transaction->details;

        return response()->json([
            'status' => 'success',
            'message' => 'Data Transaksi PO Berhasil Ditambahkan',
            'data' => $newData
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
