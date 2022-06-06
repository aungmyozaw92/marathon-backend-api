<?php

namespace App\Exports;

use App\Models\Merchant;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class MerchantData implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        return Merchant::with(
                'merchant_associates',
                'merchant_associates.phones',
                'merchant_associates.emails',
                'merchant_associates.city',
                'merchant_associates.zone',
                'account_informations',
                'city',
                'staff',
                'account'
            )->where(function ($query) {
                auth()->user()->hasRole('Agent')  ? $query : $query->where('city_id', auth()->user()->city_id);
            })
            ->filter(request()->only([
                'search', 'merchant_id', 'city_id', 'username', 'name', 'staff_id' , 
                'label', 'address', 'phone', 'email', 'account_name', 'account_no',
                'balance', 'balance_operator', 'is_deleted'
            ]))->order(request()->only([
                'sortBy', 'orderBy'
            ]))
            ->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Merchant';
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->username,
            $row->account_code,
            $row->current_sale_count,
            $row->available_coupon,
            optional($row->city)->name,
            optional($row->staff)->name,
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'name',
            'username',
            'account_code',
            'current_sale_count',
            'available_coupon',
            'city',
            'staff'
        ];
    }
}
