<?php
namespace App\Imports\Sheets;

use App\Models\AccountInformation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AccountInformationSheetImport implements ToModel,WithHeadingRow
{
    public function model(array $row)
    {
      if (request()->get('AccountInformation') == 'AccountInformation') {
          AccountInformation::create([
              'account_name'=>$row['account_name'],
              'account_no'=>$row['account_no'],
              'resourceable_type'=>$row['resourceable_type'],
              'resourceable_id'=>$row['resourceable_id'],
              'bank_id'=>isset($row['bank_id']) ? $row['bank_id'] : null,
              'is_default'=>isset($row['is_default'])? $row['is_default'] : false,
            ]);
      }
    }
}
