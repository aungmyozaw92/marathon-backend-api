<?php

namespace App\Exports;

use App\Models\Merchant;
use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class TransactionData implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        $transactions =  Transaction::with([
                            'from_account' => function ($query) {
                                $query->withTrashed();
                            },
                            'to_account'  => function ($query) {
                                $query->withTrashed();
                            },
                            'bank'  => function ($query) {
                                $query->withTrashed();
                            },
                            'account_information'  => function ($query) {
                                $query->withTrashed();
                            },
                            'account_information.bank'  => function ($query) {
                                $query->withTrashed();
                            },
                            'attachments' => function ($query) {
                                $query->withTrashed();
                            }])
                        ->filter(request()->all())
                        ->order(request()->only([
                            'sortBy', 'orderBy'
                        ]));

        if (request()->get('associated_merchant') === "true") {
            $merchants_id = Merchant::where('staff_id', auth()->user()->id)->get()->pluck('id');

            $transactions = $transactions->where(function ($q) use ($merchants_id) {
                $q->whereHas('to_account', function ($qr) use ($merchants_id) {
                    $qr->whereIn('accountable_id', $merchants_id)
                        ->where('accountable_type', 'Merchant');
                })
                ->orWhereHas('from_account', function ($qr) use ($merchants_id) {
                    $qr->whereIn('accountable_id', $merchants_id)
                        ->where('accountable_type', 'Merchant');
                });
            });
        }

        return $transactions->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Transaction';
    }

    public function map($row): array
    {
        // $s_quote = ($row->account_name)?"'" : '';
        return [
            $row->id,
            $row->transaction_no,
            $row->type,
            ($row->from_account->accountable_type == 'HQ')
                ? $row->from_account->accountable_type
                : $row->from_account->accountable->name,
            ($row->from_account->accountable_type == 'HQ')
                ? $row->to_account->accountable->city->name
                : $row->from_account->accountable->city->name,
            ($row->to_account->accountable_type == 'HQ')
                ? $row->to_account->accountable_type
                : $row->to_account->accountable->name,
            optional($row->bank)->name,
            $row->account_name,
            implode("-", str_split((string)$row->account_no, 4)) . ' ',
            $row->amount,
            $row->note,
            $row->status ? 'Confirmed' : 'Pending',
            $row->created_at,
            $row->created_by_type,
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'Transaction No',
            'Type',
            'From',
            'City',
            'To',
            'Bank Name',
            'Account Name',
            'Account Number',
            'Amount',
            'Note',
            'Status',
            'Date',
            'Created By Type'
        ];
    }

    // public function columnFormats(): array
    // {
    //     return [
    //         'I' =>  \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING,
    //     ];
    // }
}
