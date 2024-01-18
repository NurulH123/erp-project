<?php

namespace App\Http\Controllers;

use App\Models\DetailSalesOrder;
use App\Models\Product;
use App\Models\SalesOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalesOrderController extends Controller
{
    public function index()
    {
        $sort = request('sort') ?? 5;

        $user = auth()->user();
        $companyId = $user->company->id;
        $salesOrders = SalesOrder::whereHas('company', function(Builder $query) use($companyId){
                            $query->where('id', $companyId);
                        })->paginate($sort);

        return response()->json([
            'status' => 'success',
            'data' => $salesOrders
        ]);
    }

    public function show(SalesOrder $salesOrder)
    {
        return response()->json([
            'status' => 'success',
            'data' => $salesOrder
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->so, [
            'customer_id' => 'required',
            'warehouse_id' => 'required',
            'date_transaction' => 'required',
            'detail_so' => 'required',
        ], [
            'customer_id.required' => 'Customer Harus Diisi',
            'warehouse_id.required' => 'Gudang Harus Diisi',
            'date_transaction.required' => 'Tgl Transaksi Harus Diisi',
            'detail_so.required' => 'Detail Harus Diisi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ]);
        }

        $user = auth()->user();
        $company = $user->company;

        // Create transaksi sales order
        $dataSo = $request->so;
        $dataSo['code'] = date('YmdHis');
        
        $transaction = $company->transactionSo()->create($dataSo);

        // proses create detail transaksi & update stok produk
        $details = $request->detail_so;
        foreach ($details as $detail) {
            // $productWarehouse = $transaction->warehouse->products->find($detail['product_id']);
            // $stockInWarehouse = $productWarehouse->pivot->stock;
            // $currentStock = $stockInWarehouse - $detail['quantity'];

            // // validasi stok produk digudang
            // if ($currentStock < 0) return response()->json([
            //     'status' => 'failed',
            //     'message' => 'Stok '.$productWarehouse->name.' Tidak Mencukupi'
            // ]);

            $transaction->details()->create($detail); // create detail
            // $transaction->warehouse->products()->updateExistingPivot(
            //     $detail['product_id'], 
            //     ['stock' => $currentStock]
            // ); // update stok produk gudang
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data Transaksi Telah Tersimpan'
        ]);
    }

    /**
     *  Jika user sudah membuat invoice maka, maka user
     *  tidak bisa menguppdate transaksi
     */
    public function update(Request $request, SalesOrder $salesOrder)
    {
        $validator = Validator::make($request->all(), [
            'so' => 'required',
            'detail_so' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ]);
        }

        // Update transaksi sales order
        $salesOrder->update($request->so);

        // Update detail transaksi
        $details = $request->detail_so;

        foreach ($details as $detail) {
            $currentProductId = $detail['current_product_id'];
            $prevProductId = $detail['prev_product_id'];

            $prevDetailProduct = DetailSalesOrder::where('product_id', $prevProductId)->first();

            if ($currentProductId == $prevProductId) {
                if (isset($detail['quantity'])) {
                    $prevDetailProduct->update(['quantity' => $detail['quantity']]);
                }
            } else {
                $prevDetailProduct->delete(); // Hapus detail product sebelumnya
                
                // create detail baru
                $salesOrder->details()->create([
                    'product_id' => $currentProductId, 
                    'quantity' => $detail['quantity']
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data Transaksi Berhasil Diubah'
        ]);
    }
}
