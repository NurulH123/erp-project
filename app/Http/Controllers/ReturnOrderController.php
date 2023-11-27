<?php

namespace App\Http\Controllers;

use App\Models\Checkout;
use App\Models\Order;
use App\Models\Return_order;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Return_;

class ReturnOrderController extends Controller
{
    public function indexReturnOrder()
    {
        $return_order = Return_order::with('customer')->with('product')->OrderBy('id', 'desc')->get();


        return response()->json(['data' => $return_order]);
    }
    public function createReturnOrder(Request $request)
    {

        $order = Order::where('order_code', $request->order_code)->first();

        if (!$order) {
            return response()->json(['message' => 'data order tidak ditemukan']);
        }

        $checkout = Checkout::where('order_code', $request->order_code)->where('product_id', $request->product_id)->first();
        if ($request->quantity > $checkout->quantity) {
            return response()->json(['message' => 'jumlah return product tidak sesuai']);
        }

        $data = $request->validate([
            'order_code' => 'required',
            'product_id' => 'required',
            'quantity' => 'required|integer',
            'return_reason' => 'required',
            'opened' => 'required',
            'return_action' => 'required',
            'status' => 'required',
        ]);


        if ($request->comment) {
            $data['comment'] = $request->comment;
        }

        $numb = Return_order::count();
        $number = $numb + 1;
        $data['return_id'] = date("dmY$number");

        $data['order_id'] = $order->id;

        $date = Order::where('order_code', $request->order_code)->selectRaw("DATE(created_at) as created_date")->first();

        $data['order_date'] = $date->created_date;
        $data['customer_id'] = $order->customer_id;

        $return_order = Return_order::create($data);

        return response()->json(['data' => $return_order]);

    }

    public function deleteReturnOrder(Request $request, $id)
    {

        Return_order::destroy($id);

        return response()->json(['message' => 'data berhasil dihapus'], 200);

    }
}
