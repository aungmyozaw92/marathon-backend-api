<?php

namespace App\Exports;

use App\Models\Account;
use App\Models\Merchant;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class AccountSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        $accounts = Account::with(['city','merchant'])
        ->where('accountable_type', "Merchant")
        ->orderBy('accountable_id', 'asc')
        ->whereHas('merchant', function ($q){
            $q->whereNull('deleted_at');
        })->get();
        return $accounts;
        // return Merchant::whereBetween('id', [1, 3000])->orderBy('id','asc')->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Account';
    }

    public function map($row): array
    {
        return [
            $row->account_no,
            $row->city->name,
            $row->accountable_type,
            $row->accountable_id,
            optional($row->merchant)->name,
            ($row->credit) ? $row->credit : '0',
            ($row->debit) ? $row->debit : '0',
            ($row->balance) ? $row->balance : '0',
            // ($pending_balance) ? $pending_balance : '0',
            $row->created_at,
            $row->updated_at,
            $row->deleted_at,
            
        ];

        // return [
        //     $row->id,
        //     $row->name,
        //     $row->pending_balance()
           
            
        // ];
        
    }

    public function headings(): array
    {
        // return [        
        //     'id',
        //     'name',
        //     'pending_balance'
           
        // ];
        return [        
            'account_no',
            'city_name',
            'accountable_type',
            'accountable_id',
            'account_name',
            'credit',
            'debit',
            'balance',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }
}
