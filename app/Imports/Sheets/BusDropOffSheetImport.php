<?php
namespace App\Imports\Sheets;

use App\Models\BusDropOff;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BusDropOffSheetImport implements ToModel,WithHeadingRow
{
    public function model(array $row)
    {
      if (request()->get('BusDropOff') == 'BusDropOff') {
          BusDropOff::create([
              'gate_id' => $row['gate_id'],
              'global_scale_id' => $row['global_scale_id'],
              'route_id' => $row['route_id'],
              'base_rate' => isset($row['base_rate']) ? $row['base_rate'] : 0,
              'agent_base_rate' => isset($row['agent_base_rate']) ? $row['agent_base_rate'] : 0,
              'salt' => isset($row['salt']) ? $row['salt'] : 0,
            ]);
      }      
    }
}
						
