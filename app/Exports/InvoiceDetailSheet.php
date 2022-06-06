<?php

namespace App\Exports;

use App\Models\InvoiceJournal;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class InvoiceDetailSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    protected $invoice_id;

    function __construct($invoice_id) {
        $this->invoice_id = $invoice_id;
    }

    public function collection()
    {
        $invoices = InvoiceJournal::where('invoice_id', $this->invoice_id)->get();

        return $invoices;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'InvoiceDetail';
    }

    public function map($row): array
    {
        return [
            $row->invoice_no,
            // $row->merchant_id,
            // $row->debit_account_id,
            // $row->credit_account_id,
            $row->amount,
            // $row->resourceable_id,
            // $row->resourceable_type,
            $row->status,
            $row->thirdparty_invoice,
            $row->voucher_no,
            $row->weight,
            $row->pickup_date,
            $row->delivered_date,
            $row->delivery_status,
            $row->receiver_name,
            $row->receiver_city,
            $row->receiver_zone,
            $row->receiver_address,
            $row->receiver_phone,
            $row->total_amount_to_collect,
            $row->voucher_remark,
            // $row->balance_status,
            // $row->adjustment_by,
            $row->adjustment_by_name,
            $row->adjustment_date,
            $row->adjustment_note,
            $row->adjustment_amount,
            $row->diff_adjustment_amount,
        ];
    }

    public function headings(): array
    {
        return [
            // 'invoice_id',
            'Invoice No',
            // 'merchant_id',
            // 'debit_account_id',
            // 'credit_account_id',
            'Amount',
            // 'resourceable_id',
            // 'resourceable_type',
            'Status',
            'ThirdParty Invoice',
            'Voucher No',
            'Weight',
            'Pickup Date',
            'Delivered Date',
            'Delivery Status',
            'Receicer Name',
            'Receicer City',
            'Receiver Zone',
            'Receicer Address',
            'Receicer Phone',
            'COD Amount',
            'Voucher Remark',
            // 'balance_status',
            // 'adjustment_by',
            'Adjustment Name',
            'Adjustment Date',
            'Adjustment Note',
            'Adjustment Amount',
            'Diff Adjustment Amount',
        ];
    }
}
