<?php
namespace App\Imports\Sheets;

use App\Models\ContactAssociate;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ContactAssociateSheetImport implements ToModel,WithHeadingRow
{
    public function model(array $row)
    {

      if (request()->get('ContactAssociate') == 'ContactAssociate') {
          $data = ContactAssociate::create([
              'merchant_id' => $row['merchant_id'],
              'merchant_associate_id' => $row['merchant_associate_id'],
              'type' => $row['type'],
              'value' => $row['value'] ? $row['value'] : '123',
              // 'deleted_at' => isset($row['deleted_at']) ? now() : null,
            //   'deleted_by' => isset($row['deleted_by']) ? $row['deleted_by'] : null,
            ]);
        //   if ($row['deleted_by']) {
        //       $deleted = $data->delete($data->id);
        //       if ($deleted) {
        //           $data->deleted_at = now();
        //           $data->save();
        //       }
        //   }
      }
    }
}