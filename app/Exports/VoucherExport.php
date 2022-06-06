<?php

namespace App\Exports;

use App\Exports\UvVoucherSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class VoucherExport implements WithMultipleSheets
{
    use Exportable;
    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new UvVoucherSheet();
        
        return $sheets;
    }
}
