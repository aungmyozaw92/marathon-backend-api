<?php
namespace App\Imports\Sheets;

use App\Models\DoorToDoor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DoorToDoorSheetImport implements ToModel,WithHeadingRow
{
    public function model(array $row)
    {
      if (request()->get('DoorToDoor') == 'DoorToDoor') {
          DoorToDoor::create([
              'global_scale_id' => $row['global_scale_id'],
              'route_id' => $row['route_id'],
              'base_rate' => isset($row['base_rate']) ? $row['base_rate'] : 0,
              'agent_base_rate' => isset($row['agent_base_rate']) ? $row['agent_base_rate'] : 0,
              'salt' => isset($row['salt']) ? $row['salt'] : 0,
              'agent_salt' => isset($row['agent_salt']) ? $row['salt'] : 0,
            ]);
      }            
    }
}