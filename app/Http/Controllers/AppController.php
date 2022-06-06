<?php
namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Role;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function getIndex()
    {
        return view('index');
    }

    public function getAuthorPage()
    {
        return view('author');
    }
    public function getAdminPage()
    {
        $staffs = Staff::all();
        return view('admin', ['staffs' => $staffs]);
    }
    public function getGenerateArticle()
    {
        return response('Article generated!', 200);
    }

    public function postAdminAssignRoles(Request $request)
    {
        $staff = Staff::where('username', $request['username'])->first();
        $staff->roles()->detach();

        if ($request['role_delivery']) {
            $staff->roles()->attach(Role::where('name', 'Delivery')->first());
        }
        if ($request['role_cs']) {
            $staff->roles()->attach(Role::where('name', 'CS')->first());
        }
        if ($request['role_os']) {
            $staff->roles()->attach(Role::where('name', 'OS')->first());
        }
        if ($request['role_finiance']) {
            $staff->roles()->attach(Role::where('name', 'Finiance')->first());
        }
        if ($request['role_admin']) {
            $staff->roles()->attach(Role::where('name', 'Admin')->first());
        }
        return redirect()->back();
    }
}
