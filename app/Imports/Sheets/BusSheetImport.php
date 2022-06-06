<?php
namespace App\Imports\Sheets;

use App\Models\Bus;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BusSheetImport implements ToModel,WithHeadingRow
{
    public function model(array $row)
    {
        if (request()->get('Bus') == 'Bus') {
            Bus::create([
            'name' => $row['name'],
            ]);
        }
    }
}
						
