<?php
namespace App\Imports\Sheets;

use App\Models\City;
use App\Models\Meta;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CitySheetImport implements ToModel,WithHeadingRow
{
    public function model(array $row)
    {
        if (request()->get('City') == 'City') {
            $city = City::create([
                'name' => $row['name'],
                'name_mm' => $row['name_mm'],
                'is_collect_only' => $row['is_collect_only'],
                'is_on_demand' => $row['is_on_demand'],
                'is_available_d2d' => $row['is_available_d2d'] ? 1 : 0,
                'firestore_document' => $row['firestore_document'] ? $row['firestore_document'] : null,
            ]);
             if (isset($row['branch']) && $row['branch'] > 0) {
                 Meta::where('key', 'branch')->update(['value' => $city->id]);
                 Meta::where('key', 'city')->update(['value' => $city->id]);
            }
        }
    }
}