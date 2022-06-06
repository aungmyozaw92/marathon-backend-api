<?php
namespace App\Imports\Sheets;

use App\Models\Role;
use App\Models\Staff;
use App\Models\StaffRole;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StaffSheetImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (request()->get('Staff') == 'Staff' && !$row['deleted_at'] && !$row['deleted_by']) {
            $password = isset($row['password']) ? $row['password'] : Hash::make('secret');
            $staff =  Staff::create([
                'name' => $row['name'],
                'username' => isset($row['username'])?$row['username']:str_replace(' ', '', strtolower($row['name'])),
                'password' => $password,
                'role_id' => $row['role_id'],
                'department_id' => $row['department_id'],
                'zone_id' => isset($row['zone_id']) ? $row['zone_id'] : null,
                'courier_type_id' => isset($row['courier_type_id']) ? $row['courier_type_id'] : null,
                'hero_badge_id' => isset($row['hero_badge_id']) ? $row['hero_badge_id'] : null,
                'city_id' => isset($row['city_id']) ? $row['city_id'] : 64,
                'phone' => isset($row['phone']) ? ($row['phone'][0] == '0')? $row['phone']: '0'.$row['phone'] : null,
                'is_commissionable' => isset($row['is_commissionable']) ? $row['is_commissionable'] : false,
                'is_pointable' => isset($row['is_pointable']) ? $row['is_pointable'] : false,
                'staff_type' => isset($row['staff_type']) ? $row['staff_type']:'In-house',
                'car_no' => isset($row['car_no']) ? $row['car_no']:null,
                ]);
               
            $staff->roles()->detach();
            
            if ($row['role_id'] == 1) {
                $staff->roles()->attach(Role::where('name', 'Admin')->first());
            }
            if ($row['role_id'] == 2) {
                $staff->roles()->attach(Role::where('name', 'Finance')->first());
            }

            if ($row['role_id'] == 3) {
                $staff->roles()->attach(Role::where('name', 'Operation')->first());
            }
            if ($row['role_id'] == 4) {
                $staff->roles()->attach(Role::where('name', 'CustomerService')->first());
            }
            if ($row['role_id'] == 5) {
                $staff->roles()->attach(Role::where('name', 'Delivery')->first());
            }
            if ($row['role_id'] == 6) {
                $staff->roles()->attach(Role::where('name', 'HQ')->first());
            }
            if ($row['role_id'] == 7) {
                $staff->roles()->attach(Role::where('name', 'Agent')->first());
            }

            # code...
        }
    }
}
