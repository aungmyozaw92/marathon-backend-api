<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Role;
use App\Models\Staff;
use App\Models\Delivery;
use App\Models\StaffRole;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Hash;

class StaffRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Staff::class;
    }

    /**
     * @param array $data
     *
     * @return Staff
     */
    public function create(array $data): Staff
    {
        //if (isset($data['name'])) {
        $name = getConvertedString($data['name']);
        //}

        $staff = Staff::create([
            'name'              => $name,
            'username'          => $data['username'],
            'role_id'           => $data['role_id'],
            'department_id'     => $data['department_id'],
            'hero_badge_id'     => 1,
            'password'          => Hash::make($data['password']),
            'city_id'           => $data['city_id'],
            'staff_type'        => isset($data['staff_type']) ? $data['staff_type'] : 'In-house',
            'zone_id'           => isset($data['zone_id']) ? $data['zone_id'] : null,
            'courier_type_id'   => isset($data['courier_type_id']) ? $data['courier_type_id'] : null,
            'phone'             => isset($data['phone']) ? $data['phone'] : null,
            'is_commissionable' => isset($data['is_commissionable']) ? $data['is_commissionable'] : false,
            'is_pointable'      => isset($data['is_pointable']) ? $data['is_pointable'] : false,
            'car_no'            => isset($data['car_no']) ? $data['car_no'] : null,
            'created_by'        => auth()->user()->id,
        ]);

        StaffRole::create([
            'staff_id' => $staff->id,
            'role_id'  => $staff->role_id
        ]);

        if ($data) {
            # code...
        }
        $accountRepository = new AccountRepository();
        $account = [
                'city_id'          => isset($staff->zone) ? $staff->zone->city_id : null,
                'accountable_type' => 'Staff',
                'accountable_id'   => $staff->id,
            ];
        $accountRepository->create($account);

        return $staff;
    }

    /**
     * @param Staff  $staff
     * @param array $data
     *
     * @return mixed
     */
    public function update(Staff $staff, array $data): Staff
    {
        $role_id = isset($data['role_id']) ? $data['role_id'] : $staff->role_id;
        $staff->name             = isset($data['name']) ? $data['name'] : $staff->name;
        $staff->role_id          = $role_id;
        $staff->department_id    = isset($data['department_id']) ? $data['department_id'] : $staff->department_id;
        $staff->username         = isset($data['username']) ? $data['username'] : $staff->user;
        $staff->password         = isset($data['password']) ? Hash::make($data['password']) : $staff->password;
        $staff->staff_type       = isset($data['staff_type']) ? $data['staff_type'] : $staff->staff_type;
        $staff->city_id          = isset($data['city_id']) ? $data['city_id'] : $staff->city_id;
        $staff->zone_id          = isset($data['zone_id']) ? $data['zone_id'] : $staff->zone_id;
        $staff->is_commissionable = isset($data['is_commissionable']) ? $data['is_commissionable'] : $staff->is_commissionable;
        $staff->is_pointable     = isset($data['is_pointable']) ? $data['is_pointable'] : $staff->is_pointable;
        $staff->courier_type_id  = isset($data['courier_type_id']) ? $data['courier_type_id'] : null;
        $staff->phone            = isset($data['phone']) ? $data['phone'] : $staff->phone;
        $staff->car_no            = isset($data['car_no']) ? $data['car_no'] : $staff->car_no;

        if ($staff->isDirty()) {
            $staff->updated_by = auth()->user()->id;
            $staff->save();
        }

        if (!$staff->account) {
            $accountRepository = new AccountRepository();
            $account = [
                'city_id'          => isset($staff->zone) ? $staff->zone->city_id : null,
                'accountable_type' => 'Staff',
                'accountable_id'   => $staff->id,
            ];
            $accountRepository->create($account);
        }

        $staff->roles()->detach();
        
        if ($role_id == 1) {
            $staff->roles()->attach(Role::where('name', 'Admin')->first());
        }
        if ($role_id == 2) {
            $staff->roles()->attach(Role::where('name', 'Finance')->first());
        }

        if ($role_id == 3) {
            $staff->roles()->attach(Role::where('name', 'Operation')->first());
        }
        if ($role_id == 4) {
            $staff->roles()->attach(Role::where('name', 'CustomerService')->first());
        }
        if ($role_id == 5) {
            $staff->roles()->attach(Role::where('name', 'Delivery')->first());
        }
        if ($role_id == 6) {
            $staff->roles()->attach(Role::where('name', 'HQ')->first());
        }
        if ($role_id == 7) {
            $staff->roles()->attach(Role::where('name', 'Agent')->first());
        }

        return $staff->refresh();
    }

    public function assign_roles(Staff $staff, array $data): Staff
    {
        $staff->roles()->detach();

        if (isset($data['role_admin']) && $data['role_admin']) {
            $staff->roles()->attach(Role::where('name', 'Admin')->first());
        }
        if (isset($data['role_finiance']) && $data['role_finiance']) {
            $staff->roles()->attach(Role::where('name', 'Finance')->first());
        }
        if (isset($data['role_os']) && $data['role_os']) {
            $staff->roles()->attach(Role::where('name', 'OS')->first());
        }
        if (isset($data['role_cs']) && $data['role_cs']) {
            $staff->roles()->attach(Role::where('name', 'CS')->first());
        }
        if (isset($data['role_delivery']) && $data['role_delivery']) {
            $staff->roles()->attach(Role::where('name', 'Delivery')->first());
        }
        return $staff->refresh();
    }

    /**
     * @param Staff $staff
     */
    public function destroy(Staff $staff)
    {
        $deleted = $this->deleteById($staff->id);

        if ($deleted) {
            $staff->deleted_by = auth()->user()->id;
            $staff->save();
        }
    }
    public function destroy_all($data)
    {
        $deleted = $this->deleteMultipleById($data['ids']);

        if ($deleted) {
            return true;
        }
        return false;
        
    }
    
    public function reset_point(Staff $staff): Staff
    {
        $staff->points = 0;
        if ($staff->isDirty()) {
            $staff->updated_by = auth()->user()->id;
            $staff->save();
        }
        return $staff->refresh();
    }

    public function reset_points()
    {
        foreach (Delivery::all() as $staff) {
            $staff->points = 0;
            if ($staff->isDirty()) {
                $staff->updated_by = auth()->user()->id;
                $staff->save();
            }
        }
        return true;
    }
}
