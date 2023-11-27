<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderReport implements FromCollection, WithHeadings
{
    protected $dataCollection;

    public function __construct(Collection $dataCollection)
    {
        $this->dataCollection = $dataCollection;
    }

    public function collection()
    {
        // Tidak perlu lagi konversi, karena dataCollection sudah dalam format koleksi Laravel
        return $this->dataCollection;
    }

    public function headings(): array
    {
        // Tentukan heading (nama kolom) untuk file Excel
        return [
            'ID',
            'Nama',
            'Email',
            'Jumlah Order',
            'Jumlah Produk',
            'Waktu',
            'Dibuat pada',
            'Diperbarui pada',
        ];
    }
}
