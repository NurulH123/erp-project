<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\DataLead; // Ganti dengan model Anda

class DataLeadExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
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

        if ($role == 'Customer Service') {
            return DataLead::where('user_id', $user->id)->whereBetween('created_at', [$startOfDate, $endOfDate])->with('user')->get();
        } else {
            return DataLead::whereBetween('created_at', [$startOfDate, $endOfDate])->with('user')->get();
        }
    }

    public function map($data): array
    {
        $tgl = Carbon::parse($data->created_at)->translatedFormat('d M Y');

        $order = Order::whereDate('created_at', $data->created_at->toDateString())->where('user_id', $data->user_id)->where('sumber_lead', $data->sumber_lead)->where('special', '=', "false")->get();

        $closing = $order->count();
        if ($data->jumlah_lead == 0) {
            $cr = 0;
        } else {
            $cr = (100 / $data->jumlah_lead) * $order->count();
        }

        $baru = $order->where('jenis_lead', 'Lead Baru')->count();
        $lama = $order->where('jenis_lead', 'Lead Follow Up')->count();
        $ongkir = $order->sum('ongkos_kirim');
        $potongan = $order->sum('potongan_ongkir');
        $omset = $order->sum('total_price');

        return [
            $tgl,
            $data->user->username,
            $data->sumber_lead,
            $data->jumlah_lead,
            $closing,
            round($cr) . '%',
            $baru,
            $lama,
            $ongkir,
            $potongan,
            $omset,

        ];

    }


    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama CS',
            'Sumber Leads',
            'Leads',
            'Closing',
            '% CR',
            'Lead Baru',
            'Follow Up',
            'Ongkir',
            'Potongan',
            'Omset',
            'Omset yg Betul',
        ];
    }
}
