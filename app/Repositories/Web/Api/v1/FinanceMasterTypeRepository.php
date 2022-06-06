<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\FinanceMasterType;
use App\Repositories\BaseRepository;

class FinanceMasterTypeRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return FinanceMasterType::class;
    }

    /**
     * @param array $data
     *
     * @return FinanceMasterType
     */
    public function create(array $data) : FinanceMasterType
    {
        return FinanceMasterType::create([
            'name' => $data['name'],
            'description' => isset($data['description']) ? $data['description'] : null,
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param FinanceMasterType  $master_type
     * @param array $data
     *
     * @return mixed
     */
    public function update(FinanceMasterType $master_type, array $data) : FinanceMasterType
    {
        $master_type->name = $data['name'];
        $master_type->description = isset($data['description']) ? $data['description'] : $master_type->description;

        if($master_type->isDirty()) {
            $master_type->updated_by = auth()->user()->id;
            $master_type->save();
        }

        return $master_type->refresh();
    }

    /**
     * @param FinanceMasterType $master_type
     */
    public function destroy(FinanceMasterType $master_type)
    {
        $deleted = $this->deleteById($master_type->id);

        if ($deleted) {
            $master_type->deleted_by = auth()->user()->id;
            $master_type->save();
        }
    }
}

