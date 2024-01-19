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

            $detailId = $invoice['detail_sales_order_id'];

            $detail = DetailSalesOrder::find($detailId);
            $order = $detail->quantity;

            // Proses create invoice dan update stok digudang  
            $productInWarehouse = $salesOrder->warehouse->products->find($detail->product_id);

            $stockProduct  = $productInWarehouse->pivot->stock;
            $currentStock = $stockProduct - $order;
            
            $salesOrder->invoices()->create($invoice); // create invoice
            $salesOrder->warehouse->products()
                        ->updateExistingPivot(
                            $detail->product_id, 
                            ['stock' => $currentStock]
                        ); // updating stok
        }

        $totalPay = collect($invoices)->sum('pay');
        $salesOrder->update([['total_pay' => $totalPay]]);

        return response()->json([
            'status' => 'failed',
            'message' => 'Invoice Telah Dibuat' 
        ]);
    }
}
