<?php

namespace App\Exports;

use App\Models\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerOrderExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Order::join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('checkouts', 'orders.order_code', '=', 'checkouts.order_code')
            ->select(
                'customers.surename',
                'customers.phone',
                'customers.email',
                'customers.id',
                \DB::raw('SUM(checkouts.quantity) as total_product'),
                \DB::raw('SUM(orders.total_price) as total_price'),
            )
            ->where('orders.special', '=', "false")
            ->groupBy('customers.surename', 'customers.phone', 'customers.email', 'customers.id')
            ->get();
    }

    public function map($data): array
    {
        $orders = Order::where('customer_id', $data->id)->where('special', '=', "false")->get();
        $total_order = Order::where('customer_id', $data->id)->where('special', '=', "false")->count();


        $tanggal = '';
        foreach ($orders as $order) {
            $tanggal .= Carbon::parse($order->created_at)->translatedFormat('d/m/Y') . ',';
        }
        return [
            $data->surename,
            $data->phone,
            $data->email,
            $total_order,
            $data->total_product,
            $data->total_price,
            $tanggal
        ];
    }

    public function headings(): array
    {
        return [
            'Customer',
            'Nomor',
            'Email',
            'Jumlah Order',
            'Jumlah Produk',
            'Total',
            'Tanggal',
        ];
    }
}
