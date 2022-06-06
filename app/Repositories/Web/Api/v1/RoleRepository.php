<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Role;
use App\Repositories\BaseRepository;

class RoleRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Role::class;
    }

    /**
     * @param array $data
     *
     * @return Role
     */
    public function create(array $data) : Role
    {
        if (isset($data['description'])) {
            $description = getConvertedString($data['description']);
        }

        return Role::create([
            'name' => $data['name'],
            'description' => isset($data['description']) ? $description : null,
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param Role  $Role
     * @param array $data
     *
     * @return mixed
     */
    public function update(Role $role, array $data) : Role
    {
        if (isset($data['description'])) {
            $description = getConvertedString($data['description']);
        }

        $role->name = isset($data['name']) ? $data['name'] : $role->name;
        $role->description = isset($data['description']) ? $description : $role->description;

        if ($role->isDirty()) {
            $role->updated_by = auth()->user()->id;
            $role->save();
        }

        return $role->refresh();
    }

    /**
     * @param Role $role
     */
    public function destroy(Role $role)
    {
        $deleted = $this->deleteById($role->id);

        if ($deleted) {
            $role->deleted_by = auth()->user()->id;
            $role->save();
        }
    }
}
