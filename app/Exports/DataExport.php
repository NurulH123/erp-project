<?php

namespace App\Exports;

use App\Models\DataLead; // Ganti dengan model Anda
use App\Models\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DataExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        if ($this->user->role->name == 'Administrator') {
            return Order::where('special', '=', "false")->with('user')->with('customer')->with('product')->with('address')->with('checkout')->with('payment')->get();
        } else {
            return Order::where('special', '=', "false")->where('user_id', $this->user->id)->with('user')->with('customer')->with('product')->with('address')->with('checkout')->with('payment')->get();
        }
    }

    public function map($data): array
    {
        if ($data->product->count() > 0) {
            $tgl = Carbon::parse($data->created_at)->translatedFormat('d M Y');
            $harga1 = $data->product[0]->price * $data->checkout[0]->quantity;

            if ($data->product->count() == 2) {
                $harga2 = $data->product[1]->price * $data->checkout[1]->quantity;
            } else {
                $harga2 = '';
            }

            if ($data->product->count() == 3) {
                $harga3 = $data->product[2]->price * $data->checkout[2]->quantity;
            } else {
                $harga3 = '';
            }

            $voucher = $data->sub_total - ($data->total_price - $data->final_ongkir);

            if ($data->product->count() == 2) {
                return [
                    $tgl,
                    $data->user->username,
                    $data->sumber_lead,
                    $data->jenis_lead,
                    $data->customer->surename,
                    $data->address->city,
                    $data->product[0]->name,
                    $data->checkout[0]->quantity,
                    $harga1,
                    $data->product[1]->name,
                    $data->checkout[1]->quantity,
                    $harga2,
                    '',
                    '',
                    $harga3,
                    $voucher,
                    $data->ongkos_kirim,
                    $data->potongan_ongkir,
                    $data->user->id,
                    $data->total_price,
                    $data->payment->method,
                    $data->description,
                ];
            }
            if ($data->product->count() == 3) {
                return [
                    $tgl,
                    $data->user->username,
                    $data->sumber_lead,
                    $data->jenis_lead,
                    $data->customer->surename,
                    $data->address->city,
                    $data->product[0]->name,
                    $data->checkout[0]->quantity,
                    $harga1,
                    $data->product[1]->name,
                    $data->checkout[1]->quantity,
                    $harga2,
                    $data->product[2]->name,
                    $data->checkout[2]->quantity,
                    $harga3,
                    $voucher,
                    $data->ongkos_kirim,
                    $data->potongan_ongkir,
                    $data->user->id,
                    $data->total_price,
                    $data->payment->method,
                    $data->description,
                ];
            } else {
                return [
                    $tgl,
                    $data->user->username,
                    $data->sumber_lead,
                    $data->jenis_lead,
                    $data->customer->surename,
                    $data->address->city,
                    $data->product[0]->name,
                    $data->checkout[0]->quantity,
                    $harga1,
                    '',
                    '',
                    $harga2,
                    '',
                    '',
                    $harga3,
                    $voucher,
                    $data->ongkos_kirim,
                    $data->potongan_ongkir,
                    $data->user->id,
                    $data->total_price,
                    $data->payment->method,
                    $data->description,
                ];
            }
            ;
        } else {
            $tgl = Carbon::parse($data->created_at)->translatedFormat('d M Y');
            $harga1 = '';

            if ($data->product->count() == 2) {
                $harga2 = $data->product[1]->price * $data->checkout[1]->quantity;
            } else {
                $harga2 = '';
            }
            ;

            if ($data->product->count() == 3) {
                $harga3 = $data->product[2]->price * $data->checkout[2]->quantity;
            } else {
                $harga3 = '';
            }
            ;

            $voucher = $data->sub_total - ($data->total_price - $data->final_ongkir);

            return [
                $tgl,
                $data->user->username,
                $data->sumber_lead,
                $data->jenis_lead,
                $data->customer->surename,
                $data->address->city,
                '',
                '',
                $harga1,
                '',
                '',
                $harga2,
                '',
                '',
                $harga3,
                $voucher,
                $data->ongkos_kirim,
                $data->potongan_ongkir,
                $data->user->id,
                $data->total_price,
                $data->payment->method,
                $data->description,
            ];
        }

    }


    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama CS',
            'Sumber Leads',
            'Tipe Leads',
            'Nama',
            'Alamat',
            'Paket 1',
            'Qty',
            'Harga',
            'Paket 2',
            'Qty',
            'Harga',
            'Paket 3',
            'Qty',
            'Harga',
            'Voucher',
            'Ongkir',
            'Potongan Ongkir',
            'Kode',
            'Total',
            'Bank',
            'Catatan',
        ];
    }
}
