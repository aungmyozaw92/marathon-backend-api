<?php

namespace App\Exports;

use App\Models\AccountInformation;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class AccountImformationSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        return AccountInformation::withTrashed()->orderBy('id', 'asc')->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'AccountInformation';
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->account_name,
            $row->account_no,
            $row->resourceable_type,
            $row->resourceable_id,
            $row->created_by,
            $row->updated_by,
            $row->deleted_by,
            $row->created_at,
            $row->updated_at,
            $row->deleted_at,
            
            
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'account_name',
            'account_no',
            'resourceable_type',
            'resourceable_id',
            'created_by',
            'updated_by',
            'deleted_by',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }

}
