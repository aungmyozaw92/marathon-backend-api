<?php

namespace App\Exports;

use App\Models\DoorToDoor;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class DoorToDoorSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        return DoorToDoor::orderBy('id', 'asc')->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'DoorToDoor';
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->route_id,
            $row->global_scale_id,
            $row->base_rate,
            $row->agent_base_rate,
            $row->salt,
            $row->agent_salt
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'route_id',
            'global_scale_id',
            'base_rate',
            'agent_base_rate',
            'salt',
            'agent_salt'
        ];
    }

}
