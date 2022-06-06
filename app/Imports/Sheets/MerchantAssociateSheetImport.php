<?php
namespace App\Imports\Sheets;

use App\Models\ContactAssociate;
use App\Models\MerchantAssociate;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MerchantAssociateSheetImport implements ToModel,WithHeadingRow
{
    public function model(array $row)
    {
        if (request()->get('MerchantAssociate') == 'MerchantAssociate') {
            $data = MerchantAssociate::create([
              'merchant_id' => $row['merchant_id'],
              'label' => isset($row['label']) ? $row['label'] : null,
              'address' => $row['address'],
              'city_id' => $row['city_id'],
              'zone_id' => $row['zone_id'],
              'is_default' => isset($row['is_default']) ? $row['is_default'] : false,
            ]);
        }
    }
}