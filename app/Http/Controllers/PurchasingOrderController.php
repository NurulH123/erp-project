<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchasingOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class PurchasingOrderController extends Controller
{
    public function index()
    {
        $sort = request('sort') ?? '5';

        $user = auth()->user()->employee;
        $companyId = $user->company->id;
        $purchaseOrders = PurchasingOrder::whereHas('company', function(Builder $query) use($companyId){
                            $query->where('id', $companyId);
                        })
                        ->with([
                            'vendor:id,name', 
                            'warehouse:id,name',
                            'employee:code,username,email,status'
                        ])
                        ->paginate($sort);

        return response()->json([
            'status' => 'success',
            'data' => $purchaseOrders
        ]);
    }

    public function show($id)
    {
        $purchaseOrder = PurchasingOrder::with([
                            'vendor:id,name,phone,address,industry',
                            'warehouse:id,name',
                            'employee:code,username,email,status',
                            'details.product:id,name,type_zat,photo',
                            'details.invoice:detail_purchasing_order_id,come,is_completed,pay'
                        ])->find($id);

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
        
        // Data transaksi
        $user = auth()->user()->employee;
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

        return response()->json([
            'status' => 'success',
            'message' => 'Data Transaksi PO Telah Dihapus'
        ]);
    }
}
