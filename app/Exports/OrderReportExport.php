<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OrderReportExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    protected $user;
    protected $startOfDate;
    protected $endOfDate;

    public function __construct($user, $startOfDate, $endOfDate)
    {
        $this->user = $user;
        $this->startOfDate = $startOfDate;
        $this->endOfDate = $endOfDate;

    }

    public function collection()
    {
        $user = $this->user;
        $role = Role::where('id', $user->role_id)->first()->name;
        $startOfDate = Carbon::parse($this->startOfDate);
        $endOfDate = Carbon::parse($this->endOfDate)->addDays(1);
        // dd($startOfDate, $endOfDate);
        if ($role == 'Customer Service') {
            return Order::where('user_id', $user->id)->whereBetween('created_at', [$startOfDate, $endOfDate])->where('special', '=', "false")->with('customer')->with('product')->with('address')->with('payment')->get();

        } else {
            return Order::whereBetween('created_at', [$startOfDate, $endOfDate])->where('special', '=', "false")->with('customer')->with('product')->with('address')->with('payment')->get();
        }
    }

    public function map($data): array
    {

        $tgl_order = Carbon::parse($data->created_at)->translatedFormat('d/m/Y');
        $tgl_modified = Carbon::parse($data->updated_at)->translatedFormat('d/m/Y');

        $products = "";
        foreach ($data->product as $product) {
            $products .= $product->name . ', ';
        }

        $numb = 1;

        $return[] = [
            $numb++,
            $data->order_code,
            $data->customer->surename,
            $data->customer->phone,
            $data->customer->email,
            $tgl_order,
            $tgl_modified,
            $products,
            $data->product->count(),
            $data->final_ongkir,
            $data->total_price,
            $data->payment->method,
            $data->address->province,
            $data->address->city,
            $data->address->district,
            $data->address->full_address,
        ];

        return $return;

    }

    public function headings(): array
    {
        return [
            'NO',
            'Kode Order',
            'Customer',
            'No Telp',
            'Email',
            'Tgl Order',
            'Last Modified',
            'Products',
            'Total Order',
            'Ongkir',
            'Total Transfer',
            'Bank',
            'Province',
            'Kota',
            'Kecamatan',
            'Alamat Lengkap',
        ];
    }
}
