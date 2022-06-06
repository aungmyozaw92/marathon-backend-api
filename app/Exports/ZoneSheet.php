<?php

namespace App\Exports;

use App\Models\Zone;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class ZoneSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        return Zone::orderBy('id', 'asc')->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Zone';
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->name_mm,
            $row->zone_rate,
            $row->zone_agent_rate,
            $row->city_id,
            (int)$row->is_deliver,
            $row->note,
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'name',
            'name_mm',
            'zone_rate',
            'zone_agent_rate',
            'city_id',
            'is_deliver',
            'note'
        ];
    }
}
