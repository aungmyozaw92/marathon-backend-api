<?php

namespace App\Exports;

use App\Models\Staff;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class StaffSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{

    public function collection()
    {
        return Staff::withTrashed()->orderBy('id', 'asc')->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Staff';
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->role_id,
            $row->department_id,
            $row->username,
            $row->password,
            $row->phone,
            $row->is_present,
            $row->zone_id,
            $row->courier_type_id,
            $row->deleted_at,
            $row->deleted_by
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'name',
            'role_id',
            'department_id',
            'username',
            'password',
            'phone',
            // 'token',
            // 'device_token',
            'is_present',
            'zone_id',
            'courier_type_id',            
            'deleted_at',            
            'deleted_by'           
        ];
    }

}
