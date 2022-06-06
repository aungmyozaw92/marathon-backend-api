<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;


use App\Models\Role;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Staff\StaffResource;
use App\Http\Resources\Staff\StaffCollection;
use App\Http\Requests\Staff\CreateStaffRequest;
use App\Http\Requests\Staff\UpdateStaffRequest;
use App\Repositories\Web\Api\v1\StaffRepository;

class StaffController extends Controller
{
    protected $staffRepository;

    public function __construct(StaffRepository $staffRepository)
    {
        $this->staffRepository = $staffRepository;
    }

    public function index()
    {
        $staffs = Staff::with(
            'role',
            'department',
            'zone',
            'courier_type'
        )
            ->filter(request()->only(['search']))
            ->get();

        // return new StaffCollection($staffs->load(['role', 'department', 'zone', 'courier_type']));
        return new StaffCollection($staffs);
    }

    public function store(CreateStaffRequest $request)
    {
        $staff = $this->staffRepository->create($request->all());

        return new StaffResource($staff->load(['role', 'department', 'zone', 'courier_type']));
    }

    public function show(Staff $staff)
    {
        return new StaffResource($staff->load(['role', 'department', 'zone', 'courier_type']));
    }

    public function update(UpdateStaffRequest $request, Staff $staff)
    {
        $staff = $this->staffRepository->update($staff, $request->all());

        return new StaffResource($staff->load(['role', 'department', 'zone', 'courier_type']));
    }

    public function assignRoles(Request $request, Staff $staff)
    {
        $staff = $this->staffRepository->assign_roles($staff, $request->all());

        return new StaffResource($staff->load(['role', 'department', 'zone', 'courier_type']));
    }

    public function destroy(Staff $staff)
    {
        $this->staffRepository->destroy($staff);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }

    public function assign_role(Request $request)
    {
        $staffs = Staff::all();

        foreach ($staffs as $row) {
            $row->roles()->detach();
        
            if ($row->role_id == 1) {
                $row->roles()->attach(Role::where('name', 'Admin')->first());
            }
            if ($row->role_id == 2) {
                $row->roles()->attach(Role::where('name', 'Finance')->first());
            }

            if ($row->role_id == 3) {
                $row->roles()->attach(Role::where('name', 'Operation')->first());
            }
            if ($row->role_id == 4) {
                $row->roles()->attach(Role::where('name', 'CustomerService')->first());
            }
            if ($row->role_id == 5) {
                $row->roles()->attach(Role::where('name', 'Delivery')->first());
            }

        }

       // return new RoleResource($role);
    }
}
