<?php

namespace App\Exports;

use App\Models\ContactAssociate;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class ContactAssociateSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{   

    public function collection()
    {
        return ContactAssociate::withTrashed()->orderBy('id', 'asc')->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'ContactAssociate';
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->merchant_id,
            $row->merchant_associate_id,
            $row->type,
            $row->value,
            $row->deleted_at,
            $row->deleted_by,
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'merchant_id',
            'merchant_associate_id',
            'type',
            'value',
            'deleted_at',
            'deleted_by',
        ];
    }

}
