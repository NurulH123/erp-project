<?php

namespace App\Http\Controllers;

use App\Models\Checkout;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderPhoto;
use App\Models\History;
use App\Models\Product;
use App\Models\ProductCategory;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use League\CommonMark\Node\Query\OrExpr;


class OrderPhotoController extends Controller
{
    public function checkOrderPhoto(Request $request)
    {
        $data_order = Order::where('order_code', $request->order_code)->first();

        if ($data_order == null) {
            return response()->json(['message' => 'Data tidak valid']);
        }

        if ($data_order->status == "Order (Foto Belum Dikirim)" || $data_order->status == 'Komplain (Baru)') {

            $data = $request->validate([
                'order_code' => 'required',
                'phone' => 'required',
            ]);

            $data_order = Order::where('order_code', $request->order_code)->first();

            if ($data_order == null) {
                return response()->json(['message' => 'data tidak valid']);
            }

            if ($data_order->customer->phone == $request->phone) {
                $data['phone'] = $request->phone;
            } elseif ($data_order->customer->second_phone == $request->phone) {
                $data['phone'] = $request->phone;
            } else {
                return response()->json(['message' => 'nomor yang anda masukkan salah']);
            }

            $data['order_id'] = $data_order->id;
            $product_out = [];
            foreach ($data_order->product as $product) {

                $product_name['name'] = $product->name;
                $product_quantity = Checkout::where('order_code', $data_order->order_code)->where('product_id', $product->id)->first();
                $product_name['quantity'] = $product_quantity->quantity;

                $category = ProductCategory::where('product_id', $product->id)->with('category')->get();

                $category_out = [];
                foreach ($category as $frame) {

                    $category_in['name'] = $frame->category->name;
                    $category_in['quantity'] = $frame->quantity;
                    $category_in['item_id'] = "item[" . $frame->category->id . "][category_id]";
                    $category_in['item_quantity'] = "item[" . $frame->category->id . "][quantity]";

                    $category_out[] = $category_in;
                }
                $product_name['frame'] = $category_out;
                $product_out[] = $product_name;
            }

            $data['product'] = $product_out;

            return response()->json(['data' => $data], 200);

        } else {
            return response()->json(['message' => 'Foto sudah dikirim']);
        }


    }

    public function uploadOrderPhoto(Request $request)
    {

        try {
            $order = Order::where('id', $request->order_id)->where('order_code', $request->order_code)->first();

            if ($order == null) {
                return response()->json(['message' => 'data tidak valid'], 401);
            }

            $data['order_id'] = $order->id;

            $image = $request->image;

            $thismonth = Carbon::now()->month;
            $thisyear = Carbon::now()->year;
            $thisday = Carbon::now()->day;


            $folderName = 'order_photos/' . $thisyear . $thismonth . $thisday;
            $numb = 1;

            $out = [];

            OrderPhoto::where('order_id', $order->id)->delete();

            foreach ($image as $item) {

                $binaryData = $item['photo']; // Data binari dari frontend
                $mimeType = $binaryData->getClientMimeType();

                // Mendapatkan ekstensi dari mime type
                $extensionsByMimeType = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/jpeg' => 'jpeg',
                    // Tambahkan mime type lain dan ekstensinya di sini
                ];

                $extension = $extensionsByMimeType[$mimeType] ?? 'jpeg';

                // $photo = $item['photo'];

                // // Generate nama baru untuk berkas
                // $extension = $photo->getClientOriginalExtension();

                $name = $request->order_code . '-' . $item['model'] . '-00' . $numb;

                $photoPath = $item['photo']->storeAs($folderName, "$name.$extension", 'public');

                $data['path'] = $photoPath;
                $data['image_name'] = "$name.$extension";

                $order_photos = OrderPhoto::create($data);

                $out[] = $order_photos;

                $numb++;
            }
        } catch (\Throwable $e) {
            return response()->json(['message' => $e]);
        }

        $status['status'] = "Order (Foto Sudah Dikirim)";
        $order->update($status);

        // if(History::where('order_id',$request->order_id)->where('status', 'Order (Foto Sudah Dikirim)')->first() == null){
        $data_history['status'] = 'Order (Foto Sudah Dikirim)';
        $data_history['order_id'] = $request->order_id;

        History::create($data_history);
        // }

        return response()->json(['data' => $out]);

    }

    public function getOrderPhoto(Request $request, $id)
    {
        $data_order_photos = OrderPhoto::where('order_id', $id)->get();

        return response()->json(['data' => $data_order_photos]);
    }


    public function downloadOrderPhoto(Request $request)
    {
        $path = $request->path;
        $image_name = $request->image_name;


        $file = storage_path('app/public/' . $path);

        // Mendapatkan ekstensi file untuk menentukan tipe konten
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        // Daftar tipe konten yang diizinkan untuk diunduh
        $allowedContentTypes = [
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            // Tambahkan tipe konten lain sesuai kebutuhan Anda
        ];

        $contentType = $allowedContentTypes[$extension] ?? 'application/octet-stream';

        $headers = [
            'Content-Type' => $contentType,
        ];

        return response()->download($file, $image_name, $headers);
    }


    public function deleteOrderPhoto($id)
    {
        $photo = OrderPhoto::findOrFail($id); // Misalnya Anda memiliki model Photo

        // Hapus file dari penyimpanan
        Storage::disk('public')->delete($photo->path);

        // Hapus data foto dari database
        $photo->delete();

        return redirect()->back()->with('success', 'Photo deleted successfully');
    }

}
