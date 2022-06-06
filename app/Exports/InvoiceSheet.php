<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvoiceSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        $invoices = Invoice::with('merchant')->filter(request()->only([
                            'start_date', 'end_date','merchant_id'
                        ])) ->where('city_id', auth()->user()->city_id);
                        
        if (request()->get('paginate') && is_numeric(request()->get('paginate'))) {
            $paginate_count = request()->get('paginate') ? request()->get('paginate') : 25;
            $invoices = $invoices->paginate($paginate_count);
        } else {
            $invoices = $invoices->get();
        }

        return $invoices;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Invoice';
    }

    public function map($row): array
    {
        return [
            $row->invoice_no,
            $row->merchant->name,
            $row->total_voucher,
            $row->total_amount,
            $row->note,
            ($row->payment_status) ? 'Confirm' : 'Pending',
        ];
    }

    public function headings(): array
    {
        return [
            'Invoice No',
            'Merchant',
            'Total Voucher',
            'Total Amount',
            'Note',
            'Payment Status'
        ];
    }
}
