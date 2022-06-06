<?php
namespace App\Imports\Sheets;

use App\Models\Agent;
use App\Models\Account;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AgentSheetImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (request()->get('Agent') == 'Agent' && !$row['deleted_at']) {
            $agent = Agent::create([
                'name' => $row['name'],
                'username' => $row['username'],
                'password' => $password = isset($row['password']) ? $row['password'] :'secret',
                'city_id' => $row['city_id'],
                'delivery_commission' => isset($row['delivery_commission']) ? $row['delivery_commission'] : 0,
                'address' => isset($row['address']) ? $row['address'] : null,
                'phone' => isset($row['phone']) ? ($row['phone'][0] == '0')? $row['phone']: '0'.$row['phone'] : null,
                'is_active' => isset($row['is_active']) ? $row['is_active'] : false,
                'agent_badge_id' => $row['agent_badge_id'],
                'is_positive_monthly' => isset($row['is_positive_monthly']) ? $row['is_positive_monthly'] : false,
                'shop_name' => isset($row['shop_name']) ? $row['shop_name'] : null,
              ]);
             
              Account::create([
                  'city_id' => $agent->city_id,
                  'accountable_type' => 'Agent',
                  'accountable_id' => $agent->id,
              ]);
        }
    }
}
