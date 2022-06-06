<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
       // return Customer::withTrashed()->orderBy('id', 'asc')->take(22083)->get();
        return Customer::whereBetween('id', [40001, 44258])->orderBy('id', 'asc')->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Customer';
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->phone,
            $row->other_phone,
            $row->address,
            $row->point,
            $row->phone_confirmation_token,
            $row->city_id,
            $row->zone_id,
            $row->badge_id,
            $row->order,
            $row->success,
            $row->return,
            $row->rate,
            
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'name',
            'phone',
            'other_phone',
            'address',
            'point',
            'phone_confirmation_token',
            'city_id',
            'zone_id',
            'badge_id',
            'order',
            'success',
            'return',
            'rate',        
        ];
    }
}
