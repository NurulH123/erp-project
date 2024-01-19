<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchasingOrder;
use App\Models\DetailPurchasingOrder;
use Illuminate\Support\Facades\Validator;

class InvoicePurchaseOrderController extends Controller
{
    public function detailInvoice(PurchasingOrder $purchase)
    {
        $detailInvoice = PurchasingOrder::with('invoices')->find($purchase->id);
        return response()->json([
            'status' => 'success',
            'data' => $detailInvoice
        ]);
    }
    
    public function createInvoice(Request $request, PurchasingOrder $purchase)
    {
        $validator = Validator::make($request->po, ['status' => 'required'], ['status.required' => 'Status Belum Diupdate']);
        if ($validator->fails()) return response()->json(['status' => 'failed', 'message' => $validator->errors()]);

        // Buat invoice transaksi
        $invoices = collect($request->detail_po);
        $invoices->each(function($item) use($purchase){
            $detail = DetailPurchasingOrder::find($item['detail_id']);
            $detail->invoice()->create($item);

            $findProduct = $purchase->warehouse->products->find($detail->product_id);

            if (!is_null($findProduct)) {
                $stockInWarehouse = $findProduct->pivot->stock;
                $currentStock = $stockInWarehouse + $item['come'];

                $purchase->warehouse->products()->updateExistingPivot($detail->product_id, ['stock' => $currentStock]);
            } else {
                $purchase->warehouse->products()->attach($detail->product_id, ['stock' => $item['come']]);
            }
        });    

        // Update transaksi po
        $dataPo = collect($request->po)->only('status', 'desc')->toArray();
        $dataPo['total_pay'] = $invoices->sum('pay');
        $purchase->update($dataPo);

        return response()->json([
            'status' => 'success',
            'message' => 'Invoice Berhasil Dibuat'
        ]);
    }
}
