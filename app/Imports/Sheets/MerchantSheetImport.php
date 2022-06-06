<?php
namespace App\Imports\Sheets;

use App\Models\Account;
use App\Models\Merchant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MerchantSheetImport implements ToModel,WithHeadingRow
{
    public function model(array $row)
    {
      if (request()->get('Merchant') == 'Merchant') {
          $password = isset($row['password'])? $row['password'] : Hash::make('secret');
          $data =  Merchant::create([
              'name' => $row['name'],
              'username' => $row['username'],
              'password' => $password,
              'city_id' => isset($row['city_id']) ? $row['city_id'] : 64,
              'staff_id' => isset($row['staff_id']) ? $row['staff_id'] : null,
              'is_discount' => isset($row['is_discount']) ? $row['is_discount'] : true,
              'is_allow_multiple_pickups' => isset($row['is_allow_multiple_pickups']) ? $row['is_allow_multiple_pickups'] : false,
              'super_merchant_id' => isset($row['super_merchant_id']) ? $row['super_merchant_id'] : null,
              'is_root_merchant' => isset($row['is_root_merchant']) ? $row['is_root_merchant'] : false,
              'static_price_same_city' => isset($row['static_price_same_city']) ? $row['static_price_same_city'] : null,
              'static_price_diff_city' => isset($row['static_price_diff_city']) ? $row['static_price_diff_city'] : null,
              'static_price_branch' => isset($row['static_price_branch']) ? $row['static_price_branch'] : null,
              'is_corporate_merchant' => isset($row['is_corporate_merchant']) ? $row['is_corporate_merchant'] : false,
              'facebook' => isset($row['facebook']) ? $row['facebook'] : null,
              'facebook_url' => isset($row['facebook_url']) ? $row['facebook_url'] : null,
              'firebase_token' => isset($row['firebase_token']) ? $row['firebase_token'] : null,
              'default_payment_type_id' => isset($row['default_payment_type_id']) ? $row['default_payment_type_id'] : null,
              'max_withdraw_days' => isset($row['max_withdraw_days']) ? $row['max_withdraw_days'] : 2,
            ]);
          // if ($row['deleted_by']) {
          //   $deleted = $data->delete($data->id);
          //   if ($deleted) {
          //       $data->deleted_at = now();
          //       $data->save();
          //   }
          //  }
          Account::create([
            'city_id' => $data->city_id,
            'accountable_type' => 'Merchant',
            'accountable_id' => $data->id,
        ]);
      }
    }
}
			
