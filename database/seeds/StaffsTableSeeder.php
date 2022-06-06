<?php

use App\Models\Staff;
use App\Models\StaffRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class StaffsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Staff::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        factory(Staff::class)->create([
            'name'             => 'admin',
            'username'         => 'super_admin',
            'department_id'    => 1,
            'role_id'         => 1,
            'city_id'         => 64,
            
        ]);

        factory(StaffRole::class)->create([
            'staff_id'             => 1,
            'role_id'         => 1,
            
        ]);

       // factory(Staff::class, 1)->create();
        Schema::enableForeignKeyConstraints();



        // Role::create(['name' => 'admin', 'guard_name' => 'api']);
        // $staff = Staff::first();
        // $staff->assignRole('admin');
    }
}
