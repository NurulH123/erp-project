<?php

namespace App\Exports;

use Generator;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\Order;
use App\Models\Product;
use PhpParser\Node\Expr\Yield_;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ResiReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
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

            return Order::where('created_at', '>', '2023-09-01')->where('user_id', $user->id)->whereDate()->whereNotNull('nomor_resi')->where('special', '=', "false")->with('customer')->with('checkout')->with('product')->with('address')->with('payment')->get();

        } else {

            return Order::where('created_at', '>', '2023-09-01')->whereNotNull('nomor_resi')->with('customer')->where('special', '=', "false")->with('checkout')->with('product')->with('address')->with('payment')->get();

        }

    }

    public function map($data): array
    {
        $no = 1;

        $tgl_order = Carbon::parse($data->created_at)->translatedFormat('d/m/Y');
        $tgl_modified = Carbon::parse($data->updated_at)->translatedFormat('d/m/Y');

        $products = "";
        foreach ($data->product as $product) {
            $products .= $product->name . ', ';
        }

        $weight = 0;
        foreach ($data->checkout as $checkout) {
            $product_nett = Product::where('id', $checkout->product_id)->first();
            $nett = ($product_nett->weight * $checkout->quantity) / 1000;
            $weight += $nett;
        }

        return [
            $no++,
            $data->customer->surename,
            $data->customer->phone,
            $data->address->province,
            $data->address->city,
            $data->address->district,
            $data->address->full_address,
            $data->nomor_resi,
            $products,
            $tgl_order,
            $tgl_modified,
            $weight,
        ];

    }

    public function headings(): array
    {
        return [
            'NO',
            'Customer',
            'No Telp',
            'Province',
            'Kota',
            'Kecamatan',
            'Alamat',
            'Resi',
            'Products',
            'Tgl Order',
            'Last Modified',
            'Berat',
        ];
    }
}
