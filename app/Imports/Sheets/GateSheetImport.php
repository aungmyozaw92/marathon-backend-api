<?php
namespace App\Imports\Sheets;

use App\Models\Gate;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GateSheetImport implements ToModel,WithHeadingRow
{
    public function model(array $row)
    {
      if (request()->get('Gate') == 'Gate') {
          Gate::create([
              'name' => $row['name'],
              'bus_id' => $row['bus_id'],
              'bus_station_id' => $row['bus_station_id'],
              'gate_debit' => isset($row['gate_debit']) ? $row['gate_debit'] : 0,
            
            ]);
      }
    }
}
						
