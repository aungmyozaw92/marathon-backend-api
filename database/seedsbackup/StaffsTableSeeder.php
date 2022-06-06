<?php

use App\Models\Staff;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Traits\TruncateTableSeeder;

class StaffsTableSeeder extends Seeder
{
    use TruncateTableSeeder;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncate('staffs');

        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Staff::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        factory(Staff::class)->create([
            'name'             => 'admin',
            'username'         => 'super_admin',
            'department_id'         => 1,
        ]);

        factory(Staff::class)->create([
            'name'             => 'admin',
            'username'         => 'admin',
            'department_id'         => 1,
        ]);

        factory(Staff::class)->create([
            'name'             => 'admin1',
            'username'         => 'admin1',
            'department_id'         => 1,
        ]);

        factory(Staff::class)->create([
            'name'             => 'admin2',
            'username'         => 'admin2',
            'department_id'         => 1,
        ]);

        factory(Staff::class)->create([
            'name'             => 'admin3',
            'username'         => 'admin3',
            'department_id'         => 1,
        ]);

        factory(Staff::class)->create([
            'name'             => 'admin4',
            'username'         => 'admin4',
            'department_id'         => 1,
        ]);

        factory(Staff::class)->create([
            'name'             => 'admin5',
            'username'         => 'admin5',
            'department_id'         => 1,
        ]);

        factory(Staff::class)->create([
            'name'             => 'admin6',
            'username'         => 'admin6',
            'department_id'         => 1,
        ]);

        factory(Staff::class)->create([
            'name'             => 'akkm',
            'username'         => 'akkm',
            'department_id'    => 1,
        ]);

        factory(Staff::class)->create([
            'name'             => 'thiha',
            'username'         => 'thiha',
            'department_id'    => 1,
        ]);

        factory(Staff::class)->create([
            'name'             => 'amz',
            'username'         => 'amz',
            'department_id'    => 1,
        ]);

        factory(Staff::class)->create([
            'name'             => 'azb',
            'username'         => 'azb',
            'department_id'    => 1,
        ]);

        factory(Staff::class)->create([
            'name'             => 'Delivery Man',
            'username'         => 'delivery',
            'department_id'    => 5,
        ]);

        factory(Staff::class)->create([
            'name'             => 'Delivery Man 1',
            'username'         => 'delivery1',
            'department_id'    => 5,
        ]);

        factory(Staff::class, 60)->create();

        // Role::create(['name' => 'admin', 'guard_name' => 'api']);
        // $staff = Staff::first();
        // $staff->assignRole('admin');
    }
}
