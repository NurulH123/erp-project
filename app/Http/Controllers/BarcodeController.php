<?php

namespace App\Http\Controllers;

use App\Models\Checkout;
use App\Models\Order;
use Illuminate\Http\Request;
use Milon\Barcode\Facades\DNS2DFacade;
use Milon\Barcode\Facades\DNS1DFacade;
use Spatie\Browsershot\Browsershot;
use Intervention\Image\Facades\Image;


class BarcodeController extends Controller
{

    public function generateBarcode(Request $request)
    {
        $order_code = $request->order_code;

        $order = Order::where('order_code', $order_code)->first();
        if ($order == null) {
            return response()->json(['message' => 'data tidak ditemukan']);
        }

        $date = $order->created_at->format('d/m/Y');
        $customer = $order->customer->surename;
        $products = Checkout::where('order_code', $order->order_code)->get();

        $prods = "";
        foreach ($products as $product) {
            $quantity = $product->quantity;
            $item = $product->product->model;
            $prods .= "$item x $quantity <br>";
        }

        $barcode = DNS1DFacade::getBarcodeHTML($order_code, 'C128', 1.5, 65);

        $html = '<html>
                    <header></header>
                    <div style="width: 1000px; height: 280px; display: flex; border-style: ridge;">

                        <div style="width: 22%; height: 250px; padding: 15px; text-align: center; border-right: 3px solid rgb(0, 0, 0);">


                        <div style="width: 100%; height: 70px; justify-content: center; display: flex; text-align: center;"> ' . $barcode . ' </div>



                            <div style="width: 230px;font-size: 22px;" >' . $order_code . '<br>
                            ' . $date . '</div>

                        </div>

                        <div style="width: 75%; height: 250px; padding-left: 15px;">
                            <p style="font-size: 23px; font-family: Arial, sans-serif;line-height: 130%;">' . $customer . '</p>
                            <div style="line-height: 130%; font-size: 23px; font-family: Arial, sans-serif;">' . $prods . '</div>

                        </div>

                    </div>
                    <footer></footer>
                </html>'; // Ganti dengan HTML yang ingin Anda ekspor


        $outputPath = public_path("photos/$order_code.jpg"); // Tentukan path untuk menyimpan gambar hasil ekspor

        Browsershot::html($html)
            ->setOption('--headless', false)
            ->setChromePath('/usr/bin/google-chrome-stable')
            ->windowSize(1030, 300) // Atur ukuran viewport
            ->waitUntilNetworkIdle() // Tunggu hingga jaringan stabil
            ->save($outputPath);


        return response()->download($outputPath, "$order_code.jpg");
    }

}
