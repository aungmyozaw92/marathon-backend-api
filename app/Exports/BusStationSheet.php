<?php

namespace App\Exports;

use App\Models\BusStation;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class BusStationSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        return BusStation::orderBy('id', 'asc')->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'BusStation';
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->city_id,
            $row->zone_id,
            $row->number_of_gates,
            $row->delivery_rate
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'name',
            'city_id',
            'zone_id',
            'number_of_gates',
            'delivery_rate'
        ];
    }

}
