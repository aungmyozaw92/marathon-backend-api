<?php

namespace App\Exports;

use App\Models\BusDropOff;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class BusDropOffSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        return BusDropOff::orderBy('id', 'asc')->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'BusDropOff';
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->gate_id,
            $row->global_scale_id,
            $row->route_id,
            $row->base_rate,
            $row->agent_base_rate,
            $row->salt
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'gate_id',
            'global_scale_id',
            'route_id',
            'base_rate',
            'agent_base_rate',
            'salt'
        ];
    }

}
