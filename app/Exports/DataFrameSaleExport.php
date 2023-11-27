<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Checkout;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DataFrameSaleExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        $user = $this->user;
        $role = Role::where('id', $user->role_id)->first()->name;

        if ($role == 'Customer Service') {
            return Checkout::select('checkouts.order_code as order_code', 'checkouts.product_id as product_id', 'checkouts.quantity as quantity', 'checkouts.created_at as created_at', \DB::raw('DATE(checkouts.created_at) as date'))
                ->join('orders', 'checkouts.order_code', '=', 'orders.order_code')
                ->where('orders.user_id', '=', $user->id)
                ->where('orders.special', '=', 'false')
                ->get()
                ->groupBy(['date', 'product_id']);
        } else {
            return Checkout::select('checkouts.order_code as order_code', 'checkouts.product_id as product_id', 'checkouts.quantity as quantity', 'checkouts.created_at as created_at', \DB::raw('DATE(checkouts.created_at) as date'))
                ->join('orders', 'checkouts.order_code', '=', 'orders.order_code')
                ->where('orders.special', '=', 'false')
                ->get()
                ->groupBy(['date', 'product_id']);
        }

    }

    public function map($data): array
    {
        $return_out = [];
        $frames = [];
        foreach ($data as $data_child1) {
            $quantity_product = $data_child1->sum('quantity');

            $categories = ProductCategory::where('product_id', $data_child1[0]->product_id)->with('category')->get();
            foreach ($categories as $category) {
                $cek = $data_child1[0]->date . "-" . $category->category->name;
                if (isset($frames["$cek"])) {
                    $frames["$cek"]['qty'] += $category->quantity * $quantity_product;
                } else {
                    $frm['date'] = Carbon::parse($data_child1[0]->created_at)->translatedFormat('d M Y');
                    $frm['name'] = $category->category->name;
                    $frm['qty'] = $category->quantity * $quantity_product;

                    $frames[$cek] = $frm;
                }

            }
            $return_in['qty'] = $quantity_product;
            $return_out[] = $return_in;
        }

        return $frames;


    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Bingkai',
            'Quantity',
        ];
    }


}
