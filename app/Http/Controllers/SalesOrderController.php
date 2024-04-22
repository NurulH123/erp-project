<?php

namespace App\Http\Controllers;

use App\Models\COA;
use App\Models\Product;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use App\Models\DetailSalesOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class SalesOrderController extends Controller
{
    public function index()
    {
        $sort = request('sort') ?? '5';

        $user = auth()->user()->employee;
        $companyId = $user->company->id;
        $salesOrders = SalesOrder::with([
                            'warehouse:id,name', 
                            'customer:id,name,phone,address',
                            'employee:code,username,email,status',
                        ])
                        ->whereHas('company', function(Builder $query) use($companyId){
                            $query->where('id', $companyId);
                        })->paginate($sort);

        return response()->json([
            'status' => 'success',
            'data' => $salesOrders
        ]);
    }

    public function show($id)
    {
        $salesOrder = SalesOrder::with([
                    'company:id,user_id,name,category,address,phone,email,logo',
                    'warehouse:id,name', 
                    'customer:id,name,phone,address',
                    'employee:code,username,email,status',
                    'details.product:id,name,type_zat,photo',
                    'invoices',
                ])
                ->find($id);

        return response()->json([
            'status' => 'success',
            'data' => $salesOrder
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user()->employee;
        $company = $user->company;
        $coa = COA::pluck('id', 'name_account');
        $aknPiutangDagang = $coa['Piutang Dagang'];
        $aknPenjualan = $coa['Penjualan'];

        $datas  = $request->all();
        $dataSo = $datas['so'];

        // Proses validasi dan create customer jika itu adl customer baru
        if (!isset($dataSo['customer_id'])) {
            $validator = Validator::make($datas['new_customer'],[
                'name' => 'required',
                'phone' => 'required',
                'address' => 'required'
            ], [
                'name.required' => 'Nama Masih Kosong',
                'phone.required' => 'Nomor Telepon Harus Diisi',
                'address.required' => 'Alamat Masih Kosong',
            ]);

            if ($validator->fails()) return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ], 501);

            $customer = $company->customers()->firstOrCreate($datas['new_customer']);
            $dataSo['customer_id'] = $customer->id; // Menambahkan data id customer kedlm data SO
        }


        // Validasi data transaksi SO
        $validator = Validator::make($dataSo, [
            'warehouse_id' => 'required',
            'date_transaction' => 'required'
        ], [
            'warehouse_id.required' => 'Gudang Harus Diisi',
            'date_transaction.required' => 'Tanggal Transaksi Masih Kosong'
        ]);

        if ($validator->fails()) return response()->json([
            'status' => 'failed',
            'message' => $validator->errors()
        ]);



        // Proses validasi detail transaksi
        $details = $datas['detail_so'];

        foreach ($details as $item) {
            $product = Product::find($item['product_id']);
            $warehouse = $company->warehouses->find($dataSo['warehouse_id']);
            
            $productInWarehouse = $warehouse->products->find($item['product_id']);

            // Cek produk digudang 
            if (is_null($productInWarehouse)) return response()->json([
                'status' => 'failed',
                'message' => 'Produk '.$product->name. ' Tidak Tersedia Di '.$warehouse->name
            ], 501);

            $stockProduct  = $productInWarehouse->pivot->stock;
            $currentStock = $stockProduct - $item['quantity'];

            // Validasi stok produk digudang
            if ($currentStock < 0) return response()->json([
                'status' => 'failed',
                'messsage' => 'Stok '.$productInWarehouse->name.' Tidak Cukup',
            ], 501);
        }

        // Create transaksi dan details sales order
        $dataSo['code_employee'] = $user->adminEmployee->code;
        $dataSo['code_transaction'] = date('YmdHis');
        $dataSo['date_transaction'] = date('Y-m-d', strtotime($dataSo['date_transaction']));
        
        $transaction = $company->transactionSo()->create($dataSo); // create transaksi SO
        
        // Create Detail & Invoice SO
        $collDetails = collect($details);

        $collDetails->each(function($item)
        use($transaction, $aknPiutangDagang, $aknPenjualan)
        {
            $product = Product::find($item['product_id']);
            $totPrice = $product->price * $item['quantity'];

            if (!isset($item['desc'])) {$desc = null;}

            $detail = $transaction->details()->create($item); // Create detail

            // Create Invoice
            $transaction->invoices()->create([
                'detail_sales_order_id' => $detail->id,
                'total_price' => $totPrice,
                'debet' => $aknPiutangDagang,
                'kredit' => $aknPenjualan,
                'desc' => $desc
            ]);
        });

        $invoices = $transaction->invoices;
        $totPay = $invoices->sum('total_price');
        $transaction->update(['total_pay' => $totPay]);

        // Create coa transaction
        $transaction->coaTransaction()->create([
            'companiable_id' => $company->id,
            'companiable_type' => get_class($company),
            'debet' => $aknPiutangDagang,
            'kredit' => $aknPenjualan,
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Data Transaksi Telah Tersimpan',
            'data' => $transaction
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
