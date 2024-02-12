<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchasingOrder;
use App\Models\DetailPurchasingOrder;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class InvoicePurchaseOrderController extends Controller
{
    public function detailInvoice(PurchasingOrder $purchase)
    {
        $user = auth()->user();

        $detailInvoice = PurchasingOrder::with('invoices')
                            ->where('company_id', $user->company->id)
                            ->find($purchase->id);

        return response()->json([
            'status' => 'success',
            'data' => $detailInvoice
        ]);
    }
    
    public function createInvoice(Request $request, PurchasingOrder $purchase)
    {
        $validator = Validator::make($request->all(), [
            'date_accepted' => 'required',
            'detail_po' => 'required'
        ], [
            'date_accepted.required' => 'Tanggal Diterima Masih Kosong',
            'detail_po.required' => 'Detail harus Diisi'
        ]);

        if ($validator->fails()) return response()->json([
            'status' => 'failed', 
            'message' => $validator->errors()
        ], Response::HTTP_NOT_ACCEPTABLE);

        // Buat invoice transaksi
        $desc = '';
        $accepted = date('Y-m-d', strtotime($request->date_accepted));
        $invoices = collect($request->detail_po);

        $invoices->each(function($item) use($purchase, $accepted){
            $item['purchasing_order_id'] = $purchase->id;
            $item ['is_completed'] = true;

            $detail = DetailPurchasingOrder::find($item['detail_id']);
            $order = $detail->order;

            if ($order != $item['come']) $item['is_completed']  = false;

            $price = $detail->product->price;
            $item['pay'] = $price * $item['come']; // Total pay yg harus dibayar
            $detail->invoice()->create($item); // Create invoice

            // Create history
            $dataHistory = [
                'warehouse_id'  => $purchase->warehouse_id,
                'product_id'    => $detail->product_id,
                'date_accepted' => $accepted,
                'quantity'      => $item['come']
            ];

            $purchase->historyPo()->create($dataHistory);

            // Penambahan stok di gudang
            $findProduct = $purchase->warehouse->products->find($detail->product_id);

            if (!is_null($findProduct)) {
                $stockInWarehouse = $findProduct->pivot->stock;
                $currentStock = $stockInWarehouse + $item['come'];

                $purchase->warehouse->products()->updateExistingPivot($detail->product_id, ['stock' => $currentStock]);
            } else {
                $purchase->warehouse->products()->attach($detail->product_id, ['stock' => $item['come']]);
            }
        });

        if (isset($request->desc)) $desc = $request->desc;

        // Update transaksi po
        $newInvoices = $purchase->invoices;

        $dataPo['date_accepted'] = $accepted;
        $dataPo['status'] = 'accepted';
        $dataPo['desc'] = $desc;
        $dataPo['total_pay'] = $newInvoices->sum('pay');
        $purchase->update($dataPo);

        return response()->json([
            'status' => 'success',
            'message' => 'Invoice Berhasil Dibuat'
        ]);
    }
}
