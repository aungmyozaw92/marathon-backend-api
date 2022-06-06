<?php

namespace App\Exports;

use App\Models\Bus;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class BusSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        return Bus::orderBy('id', 'asc')->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Bus';
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'name'
        ];
    }

}
