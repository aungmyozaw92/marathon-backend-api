<?php
namespace App\Imports\Sheets;

use App\Models\Route;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RouteSheetImport implements ToModel,WithHeadingRow
{
    public function model(array $row)
    {
      
      if (request()->get('Route') == 'Route') {
          Route::create([
              'origin_id' => $row['origin_id'],
              'destination_id' => $row['destination_id'],
              'travel_day' => isset($row['travel_day']) ? $row['travel_day'] : 1,
              'route_name' => $row['route_name']
            ]);
      }
    }
}
						
