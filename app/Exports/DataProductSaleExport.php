<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\Product;
use App\Models\Checkout;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DataProductSaleExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
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
            return Checkout::select('checkouts.id', 'checkouts.order_code', 'checkouts.product_id', 'checkouts.quantity', 'checkouts.price', 'checkouts.created_at', 'checkouts.updated_at', \DB::raw('DATE(checkouts.created_at) as date'))
                ->join('orders', 'checkouts.order_code', '=', 'orders.order_code')
                ->where('orders.user_id', '=', $user->id)
                ->where('orders.special', '=', "false")
                ->orderBy('created_at')
                ->get()
                ->groupBy(['date', 'product_id']);
        } else {
            return Checkout::select('checkouts.id', 'checkouts.order_code', 'checkouts.product_id', 'checkouts.quantity', 'checkouts.price', 'checkouts.created_at', 'checkouts.updated_at', \DB::raw('DATE(checkouts.created_at) as date'))
                ->join('orders', 'checkouts.order_code', '=', 'orders.order_code')
                ->where('orders.special', '=', 'false')
                ->orderBy('created_at')
                ->get()
                ->groupBy(['date', 'product_id']);
        }


    }

    public function map($data): array
    {

        $return_out = [];

        foreach ($data as $data_child1) {
            $omset = 0;
            $quantity = 0;
            foreach ($data_child1 as $data_child2) {

                $tgl = Carbon::parse($data_child2->created_at)->translatedFormat('d M Y');
                $product = Product::where('id', $data_child2->product_id)->first()->name;
                $omset += $data_child2->price;
                $quantity += $data_child2->quantity;
            }
            $return_data['tanggal'] = $tgl;
            $return_data['paket'] = $product;
            $return_data['quantity'] = $quantity;
            $return_data['omset'] = $omset;

            $return_out[] = $return_data;
        }

        return $return_out;

    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Paket',
            'Quantity',
            'Omset',
        ];
    }
}
