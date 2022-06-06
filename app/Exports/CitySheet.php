<?php

namespace App\Exports;

use App\Models\City;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class CitySheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        return City::orderBy('id', 'asc')->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'City';
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->name_mm,
            (int)$row->is_collect_only,
            (int)$row->is_on_demand,
            (int)$row->is_available_d2d,
        ];
    }

    public function headings(): array
    {
        return [        
            'id',
            'name',
            'name_mm',
            'is_collect_only',
            'is_on_demand',
            'is_available_d2d'
        ];
    }
}
