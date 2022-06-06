<?php


use App\Models\StaffRole;
use Illuminate\Database\Seeder;

class StaffRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected $staff_role = [
        1  => 1,
        2  => 1,
        3  => 1,
        4  => 1,
        5  => 1,
        6  => 1,
        7  => 1,
        8  => 1,
        9  => 1,
        10 => 1,
        11 => 1,
        12 => 1,
        13 => 5,
        14 => 5
    ];

    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // StaffRole::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        foreach ($this->staff_role as $staff_id => $role_id) {
            factory(StaffRole::class)->create([
                'staff_id'       => $staff_id,
                'role_id'        => $role_id
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
