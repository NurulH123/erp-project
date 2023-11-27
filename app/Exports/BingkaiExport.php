<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BingkaiExport implements WithMultipleSheets
{

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function sheets(): array
    {
        $user = $this->user;
        $sheets = [];

        // Tambahkan lembar kerja pertama
        $sheets[] = new DataFrameExport($user);

        // Tambahkan lembar kerja kedua
        $sheets[] = new DataProductSaleExport($user);

        $sheets[] = new DataFrameSaleExport($user);

        return $sheets;
    }


}
