<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Department;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Web\Api\v1\DepartmentRepository;

class DepartmentTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function test_can_create_department()
    {
        $data = [
            'authority' => 'admin',
            'department' => 'Admin',
        ];

        $departmentRepository = new DepartmentRepository(new Department);
        $department = $departmentRepository->create($data);

        $this->assertInstanceOf(Department::class, $department);
        $this->assertEquals($data['authority'], $department->authority);
        $this->assertEquals($data['department'], $department->department);
    }

    /** @test */
    public function test_can_show_department()
    {
        $department = factory(Department::class)->create();
        $departmentRepository = new DepartmentRepository(new Department);
        $found = $departmentRepository->getById($department->id);

        $this->assertInstanceOf(Department::class, $found);
        $this->assertEquals($found->authority, $department->authority);
        $this->assertEquals($found->department, $department->department);
    }

    /** @test */
    public function test_can_update_department()
    {
        $department = factory(Department::class)->create();

        $data = [
            'authority' => 'admin',
            'department' => 'Admin',
        ];

        $departmentRepository = new DepartmentRepository(new Department);
        $updatedDepartment = $departmentRepository->update($department, $data);

        $this->assertInstanceOf(Department::class, $updatedDepartment);
        $this->assertEquals($data['authority'], $updatedDepartment->authority);
        $this->assertEquals($data['department'], $updatedDepartment->department);
    }

    /** @test */
    public function test_can_delete_department()
    {
        $department = factory(Department::class)->create();
        $departmentRepository = new DepartmentRepository(new Department);
        $delete = $departmentRepository->deleteById($department->id);

        $this->assertTrue($delete);
    }
}
