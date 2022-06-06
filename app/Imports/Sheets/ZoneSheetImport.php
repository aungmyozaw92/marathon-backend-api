<?php
namespace App\Imports\Sheets;

use App\Models\Zone;
use App\Models\Branch;
use App\Models\Account;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ZoneSheetImport implements ToModel, WithHeadingRow
{
    
    public function model(array $row)
    {
        if (request()->get('Zone') == 'Zone') {
            $zone = Zone::create([
                'name' => $row['name'],
                'name_mm' => $row['name_mm'],
                'city_id' => (int)$row['city_id'],
                'zone_rate' => isset($row['zone_rate']) ? $row['zone_rate'] : 0,
                'zone_agent_rate' => isset($row['zone_agent_rate']) ? $row['zone_agent_rate'] : 0,
                'note' => isset($row['note']) ? $row['note'] : null,
                'is_deliver' => isset($row['is_deliver']) ? $row['is_deliver'] : 0,
                'zone_commission' => isset($row['zone_commission']) ? $row['zone_commission'] : 0,
                'outsource_rate' => isset($row['outsource_rate']) ? $row['outsource_rate'] : 0,
                'outsource_car_rate' => isset($row['outsource_car_rate']) ? $row['outsource_car_rate'] : 0,
                ]);

            // if (isset($row['branch']) && $row['branch'] > 0) {
            //     $branch = Branch::create([
            //         'name' => $row['name'],
            //         'city_id' => $row['city_id'],
            //         'zone_id' => $zone->id,
            //         'delivery_commission' => isset($row['delivery_commission']) ? $row['delivery_commission'] : 1000,
            //     ]);

            //     Account::create([
            //         'city_id'          => $row['city_id'],
            //         'accountable_type' => 'Branch',
            //         'accountable_id'   => $branch->id
            //     ]);
            // }
        }
    }
}
