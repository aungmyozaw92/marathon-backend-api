<?php
namespace App\Imports\Sheets;

use App\Models\BusStation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BusStationSheetImport implements ToModel,WithHeadingRow
{
    public function model(array $row)
    {
      if (request()->get('BusStation') == 'BusStation') {
          BusStation::create([
              'name' => $row['name'],
              'name_mm' => isset($row['name_mm'])?$row['name_mm']:null,
              'city_id' => $row['city_id'],
              'zone_id' => $row['zone_id'],
              'number_of_gates' => isset($row['number_of_gates']) ? $row['number_of_gates'] : 1,
              'delivery_rate' => isset($row['delivery_rate']) ? $row['delivery_rate'] : 0,
            ]);
      }
    }
}
						
