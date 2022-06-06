<?php

namespace App\Exports;

use App\Models\CommissionLog;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class CommissionLogData implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        $commission_logs = CommissionLog::with(['staff', 'zone'])
                            ->filter(request()->all())
                            // ->where('staff_id', auth()->user()->id)
                            ->whereHas('staff', function ($query) {
                                $query->where('city_id', auth()->user()->city_id);
                            })
                            ->order(request()->only([
                                'sortBy', 'orderBy'
                            ]));

        return $commission_logs->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Commission Log';
    }

    public function map($row): array
    {
        return [
            $row->commissionable_type,
            $row->commissionable_type === 'Deduction'
                ? optional($row->commissionable)->description
                : $row->commissionable_type === 'Pickup'
                ? optional($row->commissionable)->pickup_invoice
                : $row->commissionable_type === 'DeliSheet'
                ? optional($row->commissionable)->delisheet_invoice
                : $row->commissionable_type === 'Waybill'
                ? optional($row->commissionable)->waybill_invoice
                : $row->commissionable_type === 'ReturnSheet'
                ? optional($row->commissionable)->return_sheet_invoice
                : $row->commissionable_type === 'Journal'
                ? optional($row->commissionable)->journal_no
                : $row->commissionable_id,
            optional($row->staff)->name,
            optional($row->staff)->staff_type,
            $row->zone_commission,
            $row->voucher_commission,
            optional($row->zone)->name,
            $row->num_of_vouchers,
            $row->created_at
        ];
    }
    
    public function headings(): array {
        return [
            'Resource',
            'Resource ID',
            'Hero',
            'Staff Type',
            'Base Commission',
            'Voucher Commission',
            'Base Zone',
            'Number of Vouchers',
            'Date'

        ];
    }
}
