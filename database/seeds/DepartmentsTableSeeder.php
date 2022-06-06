<?php

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentsTableSeeder extends Seeder
{
    protected $departments = [
        'admin' => 'Admin',
        'finance' => 'Finance',
        'operation' => 'Operation',
        'cs' => 'Customer Service',
        'delivery' => 'Delivery',
        'hq' => 'HQ',
        'agent' => 'Agent'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Department::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        foreach ($this->departments as $key => $value) {
            factory(Department::class)->create([
                'authority'    => $key,
                'department' => $value
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
