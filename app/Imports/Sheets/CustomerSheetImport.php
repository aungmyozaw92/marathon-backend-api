<?php
namespace App\Imports\Sheets;

use App\Models\Customer;
use App\Models\Voucher;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerSheetImport implements ToModel,WithHeadingRow
{
    // public function model(array $row)
    // {
    //   Log::info($row['name']);

    //         Customer::create([
    //           'id' => $row['id'],
    //           'name' => $row['name'],
    //           'phone' => $row['phone'],
    //           'other_phone' => $row['other_phone'],
    //           'address' => $row['address'],
    //           'point' => $row['point'],
    //           'phone_confirmation_token' => $row['phone_confirmation_token'],
    //           'city_id' => $row['city_id'],
    //           'zone_id' => $row['zone_id'],
    //           'badge_id' => $row['badge_id'],
    //           'order' => ($row['order']) ? $row['order'] : 0,
    //           'success' => ($row['success']) ? $row['success'] : 0,
    //           'return' => ($row['return']) ? $row['return'] : 0,
    //           'rate' => ($row['rate']) ? $row['rate'] : 0,
              
    //         ]);
    // }

    public function model(array $row)
    {
      // if (isset($row['id']) && isset($row['receiver_id']) ) {
      //   Voucher::where('id',$row['id'])->update(['receiver_id' => $row['receiver_id']]);
      //   // $voucher = Voucher::find($row['id']);
      //   // $voucher->receiver_id = $row['receiver_id'];
      //   // $voucher->save();
      // }
            // Customer::create([
            //   'id' => $row['id'],
            //   'name' => $row['name'],
            //   'phone' => $row['phone'],
            //   'other_phone' => $row['other_phone'],
            //   'address' => $row['address'],
            //   'point' => $row['point'],
            //   'phone_confirmation_token' => $row['phone_confirmation_token'],
            //   'city_id' => $row['city_id'],
            //   'zone_id' => $row['zone_id'],
            //   'badge_id' => $row['badge_id'],
            //   'order' => ($row['order']) ? $row['order'] : 0,
            //   'success' => ($row['success']) ? $row['success'] : 0,
            //   'return' => ($row['return']) ? $row['return'] : 0,
            //   'rate' => ($row['rate']) ? $row['rate'] : 0,
              
            // ]);
    }
}
						
