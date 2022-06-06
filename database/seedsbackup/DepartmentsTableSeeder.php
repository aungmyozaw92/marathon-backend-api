<?php

use App\Models\Department;
use Illuminate\Database\Seeder;
use App\Traits\TruncateTableSeeder;

class DepartmentsTableSeeder extends Seeder
{
    use TruncateTableSeeder;
    
    protected $departments = [
        'admin' => 'Admin',
        'finance' => 'Finance',
        'operation' => 'Operation',
        'cs' => 'Customer Service',
        'delivery' => 'Delivery'
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
        $this->truncate('departments');

        foreach ($this->departments as $key => $value) {
            factory(Department::class)->create([
               'authority'    => $key,
               'department' => $value
            ]);
        }
    }
}
