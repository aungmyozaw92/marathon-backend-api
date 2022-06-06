<?php

namespace App\Exports;

use App\Models\Merchant;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class MerchantSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        return Merchant::withTrashed()->orderBy('id', 'asc')->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Merchant';
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->username,
            $row->password,
            $row->current_sale_count,
            $row->available_coupon,
            $row->city_id,
            $row->staff_id,
            $row->deleted_at,
            $row->deleted_by,
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'name',
            'username',
            'password',
            'current_sale_count',
            'available_coupon',
            'city_id',
            'staff_id',
            'deleted_at',
            'deleted_by',

        ];
    }
}
