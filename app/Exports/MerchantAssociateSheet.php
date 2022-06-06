<?php

namespace App\Exports;

use App\Models\MerchantAssociate;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class MerchantAssociateSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        return MerchantAssociate::withTrashed()->orderBy('id', 'asc')->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'MerchantAssociate';
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->merchant_id,
            $row->label,
            $row->address,
            $row->city_id,
            $row->zone_id,
            $row->deleted_at,
            $row->deleted_by,
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'merchant_id',
            'label',
            'address',
            'city_id',
            'zone_id',
            'deleted_at',
            'deleted_by'
        ];
    }
}
