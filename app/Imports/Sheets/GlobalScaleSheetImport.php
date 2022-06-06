<?php
namespace App\Imports\Sheets;

use App\Models\GlobalScale;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GlobalScaleSheetImport implements ToModel,WithHeadingRow
{
    public function model(array $row)
    {
      if (request()->get('GlobalScale') == 'GlobalScale') {
          GlobalScale::create([
              'cbm' => $row['cbm'],
              'support_weight' => $row['support_weight'],
              'max_weight' => $row['max_weight'],
              'description' => $row['description'],
              'description_mm' => $row['description_mm'],
            ]);
      } 
    }
}
						
