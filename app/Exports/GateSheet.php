<?php

namespace App\Exports;

use App\Models\Gate;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class GateSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        return Gate::orderBy('id', 'asc')->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Gate';
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->bus_station_id,
            $row->bus_id,
            (int)$row->gate_debit
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'name',
            'bus_station_id',
            'bus_id',
            'gate_debit'
        ];
    }

}
