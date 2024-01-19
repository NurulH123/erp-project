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
        $dataSo['code_employee'] = $user->adminEmployee->code;
        $dataSo['code_transaction'] = date('YmdHis');
        $dataSo['date_transaction'] = date('Y-m-d', strtotime($dataSo['date_transaction']));
        
        $transaction = $company->transactionSo()->create($dataSo);

        // proses create detail transaksi
        $newDetails = [];
        $details = $dataSo['detail_so'];

        foreach ($details as $item) {
            $detail = collect($transaction->details()->create($item)); // create detail
            array_push(
                $newDetails, 
                $detail
                    ->except('created_at', 'updated_at', 'sales_order_id')
                    ->toArray()
            );
        }

        // Membuat data baru
        $data = $transaction->toArray();
        $data['details'] = $newDetails;

        return response()->json([
            'status' => 'success',
            'message' => 'Data Transaksi Telah Tersimpan',
            'data' => $data
        ]);
    }

    /**
     *  Jika user sudah membuat invoice maka, maka user
     *  tidak bisa menguppdate transaksi
     */
    public function update(Request $request, SalesOrder $salesOrder)
    {
        $validator = Validator::make($request->all(), [
            'so' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ]);
        }


        $data = $request->so;
        $dataSo = collect($data)->except('detail_so')->toArray();

        if (isset($dataSo['date_transaction'])) {
            $dataSo['date_transaction'] = date('Y-m-d', strtotime($dataSo['date_transaction']));
        }

        // Update transaksi sales order
        $salesOrder->update($dataSo);

        // Update detail transaksi
        $details = $data['detail_so'];

        foreach ($details as $item) {
            $validator = Validator::make($item, [
                'detail_id' => 'required',
                'product_id' => 'required',
                'quantity' => 'required',
            ], [
                'detail_id.required' => 'Id Detail Harus Diisi',
                'product_id.required' => 'Produk Harus Diisi',
                'quantity.required' => 'Jumlah Produk Harus Diisi'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $validator->errors()
                ]);
            }

            // proses update detail transaksi
            $detail =DetailSalesOrder::find($item['detail_id']);
            $detail->update($validator->getData());

        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data Transaksi Berhasil Diubah'
        ]);
    }
}
