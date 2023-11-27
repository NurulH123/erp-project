<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Checkout;
use App\Models\ProductCategory;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\DataLead; // Ganti dengan model Anda

class DataFrameExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        $user = $this->user;
        $role = Role::where('id',$user->role_id)->first()->name;

        if($role == 'Customer Service'){
            return Checkout::where('user_id', $user->id)->with('product')->get();
        } else {
            return Checkout::with('product')->get();
        }
    }

    public function map($data): array
    {
        if($data->order->special == "true"){
            return [];
        }else{
            $tgl = Carbon::parse($data->created_at)->translatedFormat('d M Y');
            $product = $data->product->name;
            $qty = $data->quantity;
            $price = $data->product->price;
            $omset = $data->price;
            $frames = Product::where('id',$data->product_id)->with('productcategory')->with('category')->get();
            foreach($frames as $frames){
                $count = $frames->category->count();
                // foreach($frames->category as $category){
                //     $categories[] = $category->name;
                // }
                foreach($frames->productcategory as $category){
                    $category['quantity'] = $category->quantity;
                    $category['name'] = Category::where('id',$category->category_id)->first()->name;
                    $categories[] = $category;
                }
            }

            // return $categories;

            if($count == 3){
                return [
                    $tgl,
                    $product,
                    $qty,
                    $price,
                    $omset,
                    $categories[0]->name,
                    $categories[0]->quantity * $qty,
                    $categories[1]->name,
                    $categories[1]->quantity * $qty,
                    $categories[2]->name,
                    $categories[2]->quantity * $qty,
                ];
            }
            elseif($count == 2){
                return [
                    $tgl,
                    $product,
                    $qty,
                    $price,
                    $omset,
                    $categories[0]->name,
                    $categories[0]->quantity * $qty,
                    $categories[1]->name,
                    $categories[1]->quantity * $qty,
                ];
            }
            elseif($count == 1){
                return [
                    $tgl,
                    $product,
                    $qty,
                    $price,
                    $omset,
                    $categories[0]->name,
                    $categories[0]->quantity * $qty,
                ];
            }

        }



    }


    public function headings(): array
    {
        return [
            'Tanggal',
            'Paket',
            'Qty',
            'Harga',
            'Omset',
            'Bingkai A',
            'Qty A',
            'Bingkai B',
            'Qty B',
            'Bingkai C',
            'Qty C'
        ];
    }
}
