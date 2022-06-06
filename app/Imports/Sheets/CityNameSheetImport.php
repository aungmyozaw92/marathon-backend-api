<?php
namespace App\Imports\Sheets;

use App\Models\City;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CityNameSheetImport implements ToModel,WithHeadingRow
{
    public function model(array $row)
    {
       $city = City::find($row['id']);
       $city->name_mm = $row['name_mm'];
       $city->save();            
    }
}
						
