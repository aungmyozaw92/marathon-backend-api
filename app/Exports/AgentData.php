<?php

namespace App\Exports;

use App\Models\Agent;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class AgentData implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        return Agent::with(['city','account','agent_badge'])
                    ->filter(request()->only([
                        'search', 'city_id','username', 'name', 'phone',
                        'is_active', 'balance', 'balance_operator', 'agent_badge_id',
                        'shop_name'
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
        return 'Agent';
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->account_code,
            $row->name,
            $row->username,
            optional($row->agent_badge)->name,
            optional($row->city)->name,
            $row->phone,
            $row->address,
            $row->delivery_commission,
            $row->rewards,
            $row->weekly_collected_amount,
            $row->monthly_collected_amount,
            $row->account->balance ? $row->account->balance : '0',
            $row->pending_balance() ? $row->pending_balance() : '0',
            $row->is_active ? 'Active' : 'Inactive'
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'Account Code',
            'Name',
            'User Name',
            'Badge',
            'City',
            'Phone',
            'Address',
            'Delivery Commission',
            'Rewards',
            'Weekly Points',
            'Monthly Points',
            'Account Balance',
            'Pending Balance',
            'Active Status'
        ];
    }
}
