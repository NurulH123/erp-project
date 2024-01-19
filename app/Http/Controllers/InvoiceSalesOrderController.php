<?php

namespace App\Http\Controllers;

use App\Models\DetailSalesOrder;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceSalesOrderController extends Controller
{
    public function createInvoice(Request $request, SalesOrder $salesOrder)
    {
        $validator = Validator::make($request->all(), [
            'invoice_so' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ]);
        }

        $invoices = $request->invoice_so;

        foreach ($invoices as $invoice) {
            $validator = Validator::make($invoice, [
                'detail_id' => 'required',
                'price' => 'required',
                'pay' => 'required',
            ], [
                'detail_id.required' => 'Id Detail Harus Diisi',
                'price.required' => 'Harga Per Satuan Harus Diisi',
                'pay.required' => 'Total Pembayaran Harus Diisi',
            ]);

            $detailId = $invoice['detail_id'];

            $detail = DetailSalesOrder::find($detailId);
            $order = $detail->quantity;

            // Proses create invoice dan update stok digudang  
            $productInWarehouse = $salesOrder->warehouse->products->find($detail[['product_id']]);
            $stockProduct  = $productInWarehouse->pivot->stock;
            $currentStock = $stockProduct - $order;

            if ($currentStock < 0) return response()->json([
                'status' => 'failed',
                'messsage' => 'Stok '.$productInWarehouse->name.' Tidak Cukup',
            ]);

            $salesOrder->invoices()->create($invoice);
            $salesOrder->warehouse->products()
                        ->updateExistingPivot($detail->product_id, ['stock' => $currentStock]);
        }

        return response()->json([
            'status' => 'failed',
            'message' => 'Invoice Telah Dibuat' 
        ]);
    }
}
