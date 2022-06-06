<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Department;
use App\Repositories\BaseRepository;

class DepartmentRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Department::class;
    }

    /**
     * @param array $data
     *
     * @return Department
     */
    public function create(array $data) : Department
    {
        return Department::create([
            'authority' => $data['authority'],
            'department' => $data['department'],
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param Department  $department
     * @param array $data
     *
     * @return mixed
     */
    public function update(Department $department, array $data) : Department
    {
        $department->authority = $data['authority'];
        $department->department = $data['department'];

        if($department->isDirty()) {
            $department->updated_by = auth()->user()->id;
            $department->save();
        }

        return $department->refresh();
    }

    /**
     * @param Department $department
     */
    public function destroy(Department $department)
    {
        $deleted = $this->deleteById($department->id);

        if ($deleted) {
            $department->deleted_by = auth()->user()->id;
            $department->save();
        }
    }
}

