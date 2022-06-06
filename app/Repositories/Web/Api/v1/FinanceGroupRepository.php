<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\FinanceGroup;
use App\Repositories\BaseRepository;

class FinanceGroupRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return FinanceGroup::class;
    }

    /**
     * @param array $data
     *
     * @return FinanceGroup
     */
    public function create(array $data) : FinanceGroup
    {
        return FinanceGroup::create([
            'name' => $data['name'],
            'description' => isset($data['description']) ? $data['description'] : null,
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param FinanceGroup  $finance_group
     * @param array $data
     *
     * @return mixed
     */
    public function update(FinanceGroup $finance_group, array $data) : FinanceGroup
    {
        $finance_group->name = $data['name'];
        $finance_group->description = isset($data['description']) ? $data['description'] : $finance_group->description;

        if($finance_group->isDirty()) {
            $finance_group->updated_by = auth()->user()->id;
            $finance_group->save();
        }

        return $finance_group->refresh();
    }

    /**
     * @param FinanceGroup $finance_group
     */
    public function destroy(FinanceGroup $finance_group)
    {
        $deleted = $this->deleteById($finance_group->id);

        if ($deleted) {
            $finance_group->deleted_by = auth()->user()->id;
            $finance_group->save();
        }
    }
}

