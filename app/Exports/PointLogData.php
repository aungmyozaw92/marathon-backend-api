<?php

namespace App\Exports;

use App\Models\PointLog;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class PointLogData implements FromCollection, WithTitle, WithHeadings, WithMapping
{
    public function collection()
    {
        $point_logs = PointLog::with(['staff', 'hero_badge', 'attachments'])
                            ->filter(request()->all())
                            ->whereHas('staff', function ($query) {
                                $query->where('city_id', auth()->user()->city_id);
                            })
                            ->order(request()->only([
                                'sortBy', 'orderBy'
                            ]));

        return $point_logs->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Point Log';
    }

    public function map($row): array
    {
        return [
            $row->id,
            optional($row->staff)->name,
            $row->points,
            $row->status,
            $row->resourceable_type,
            $row->resourceable_type === 'Deduction'
                ? optional($row->resourceable)->description
                : $row->resourceable_type === 'Pickup'
                ? optional($row->resourceable)->pickup_invoice
                : $row->resourceable_type === 'DeliSheet'
                ? optional($row->resourceable)->delisheet_invoice
                : $row->resourceable_type === 'Waybill'
                ? optional($row->resourceable)->waybill_invoice
                : $row->resourceable_type === 'ReturnSheet'
                ? optional($row->resourceable)->return_sheet_invoice
                : $row->resourceable_type === 'Journal'
                ? optional($row->resourceable)->journal_no
                : $row->resourceable_id,
            optional($row->hero_badge)->name,
            $row->note,
            $row->created_at
        ];
    }

    public function headings(): array {
        return [
            'id',
            'Hero',
            'Point',
            'Status',
            'Resource',
            'Resource ID',
            'Hero Badge',
            'Note',
            'Date',

        ];
    }
}
