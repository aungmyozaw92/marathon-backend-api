<?php

namespace App\Exports;

use App\Models\Route;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class RouteSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        return Route::orderBy('id', 'asc')->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Route';
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->origin_id,
            $row->route_name,
            $row->destination_id,
            $row->travel_day,
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'origin_id',
            'route_name',
            'destination_id',
            'travel_day'
        ];
    }

}
