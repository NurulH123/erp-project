<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ShippingReportExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        $user = $this->user;
        $orders = Order::join('users', 'orders.user_id', '=', 'users.id')
            ->join('addresses', 'orders.address_id', '=', 'addresses.id')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->select(
                'orders.order_code',
                'orders.ongkos_kirim',
                'orders.potongan_ongkir',
                'orders.final_ongkir',
                'users.username',
                'addresses.province',
                'addresses.city',
                'addresses.district',
                'addresses.postal_code',
                'addresses.full_address',
                'customers.surename',
                'customers.phone',
                'customers.email'
            )
            ->where('orders.special', '=', "false")
            ->get();

        return $orders;
    }

    public function map($data): array
    {
        $tgl_order = Carbon::parse($data->created_at)->translatedFormat('d/m/Y');
        $tgl_modified = Carbon::parse($data->updated_at)->translatedFormat('d/m/Y');

        $numb = 1;

        return [
            $numb++,
            $data->order_code,
            $data->surename,
            $data->phone,
            $data->email,
            $tgl_order,
            $tgl_modified,
            $data->full_address,
            $data->final_ongkir,
            $data->province,
            $data->city,
            $data->district,
        ];
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
            'Full Address',
            'Ongkir',
            'Province',
            'Kota',
            'Kecamatan',
        ];
    }
}

