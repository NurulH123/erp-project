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
        $datas  = $request->all();
        foreach ($datas as $key => $data) {
            $rules = [
                'warehouse_id' => 'required',
                'date_transaction' => 'required',
            ];
            $message = [
                'warehouse_id.required' => 'Customer Harus Diisi',
                'date_transaction.required' => 'Tanggal Transaksi Harus Diisi'
            ];

            if ($key == 'so') {
                if (isset($data['customer_id'])) {
                    $rules['customer_id'] = 'required';
                    $message['customer_id.required'] = 'Customer Harus Diisi';
                }

                $validator = Validator::make($data, $rules, $message);
            }

            if ($key == 'detail_so') {

            }

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $validator->errors()
                ]);
            }
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
            $product = Product::find($item['product_id']);
            $productInWarehouse = $transaction->warehouse->products->find($item['product_id']);

            // validasi ketersediaan produk digudang 
            if (is_null($productInWarehouse)) return response()->json([
                'status' => 'failed',
                'message' => 'Produk '.$product->name. ' Tidak Tersedia Di '.$transaction->warehouse->name
            ]);

            $stockProduct  = $productInWarehouse->pivot->stock;
            $currentStock = $stockProduct - $item['quantity'];

            // validasi stok digudang
            if ($currentStock < 0) return response()->json([
                'status' => 'failed',
                'messsage' => 'Stok '.$productInWarehouse->name.' Tidak Cukup',
            ]);

             // create detail 
            $detail = collect($transaction->details()->create($item));
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
        $newDetails = [];
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

            $product = Product::find($item['product_id']);
            $productInWarehouse = $salesOrder->warehouse->products->find($item['product_id']);

            // validasi ketersediaan produk digudang 
            if (is_null($productInWarehouse)) return response()->json([
                'status' => 'failed',
                'message' => 'Produk '.$product->name. ' Tidak Tersedia Di '.$salesOrder->warehouse->name
            ]);

            $stockProduct  = $productInWarehouse->pivot->stock;
            $currentStock = $stockProduct - $item['quantity'];

            // validasi stok digudang
            if ($currentStock < 0) return response()->json([
                'status' => 'failed',
                'messsage' => 'Stok '.$productInWarehouse->name.' Tidak Cukup',
            ]);

            // proses update detail transaksi
            $detail =DetailSalesOrder::find($item['detail_id']);
            $detail->update($validator->getData());

            $newDetail = collect(DetailSalesOrder::find($item['detail_id']))
                            ->only('id', 'product_id', 'quantity')
                            ->toArray();

            array_push($newDetails, $newDetail);
        }

        $data = SalesOrder::find($salesOrder->id)->toArray();
        $data['details'] = $newDetails;

        return response()->json([
            'status' => 'success',
            'message' => 'Data Transaksi Berhasil Diubah',
            'data' => $data
        ]);
    }
}
