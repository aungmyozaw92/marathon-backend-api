<?php

namespace App\Exports;

use App\Models\Agent;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AgentSheet implements FromCollection, WithTitle, WithHeadingRow
{
   use Exportable;

    public function collection()
    {
        return Agent::orderBy('id', 'asc')->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Agent';
    }

    public function headings(): array
    {
        return [
            'id',
            'name',
            'name_mm',
            'is_collect_only',
            'is_on_demand',
            'is_available_d2d',
            'created_by',
            'updated_by',
            'deleted_by',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }

}
