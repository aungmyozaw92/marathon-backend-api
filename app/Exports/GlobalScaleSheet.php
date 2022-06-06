<?php

namespace App\Exports;

use App\Models\GlobalScale;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class GlobalScaleSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        return GlobalScale::orderBy('id', 'asc')->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'GlobalScale';
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->cbm,
            $row->support_weight,
            $row->max_weight,
            $row->description,
            $row->description_mm
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'cbm',
            'support_weight',
            'max_weight',
            'description',
            'description_mm'
        ];
    }

}
