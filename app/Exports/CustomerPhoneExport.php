<?php

namespace App\Exports;

use App\Models\Customer;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerPhoneExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Customer::get();
    }

    public function map($data): array
    {
        return [
            $data->surename,
            $data->first_name,
            $data->last_name,
            $data->phone,
            $data->second_phone,
            $data->email,
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'Nama Depan',
            'Nama Belakang',
            'No. Hp',
            'No. HP 2',
            'Email',
        ];
    }
}
