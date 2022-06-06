<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\FinanceNature;
use App\Repositories\BaseRepository;

class FinanceNatureRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return FinanceNature::class;
    }

    /**
     * @param array $data
     *
     * @return FinanceNature
     */
    public function create(array $data) : FinanceNature
    {
        return FinanceNature::create([
            'name' => $data['name'],
            'description' => isset($data['description']) ? $data['description'] : null,
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param FinanceNature  $finance_nature
     * @param array $data
     *
     * @return mixed
     */
    public function update(FinanceNature $finance_nature, array $data) : FinanceNature
    {
        $finance_nature->name = $data['name'];
        $finance_nature->description = isset($data['description']) ? $data['description'] : $finance_nature->description;

        if($finance_nature->isDirty()) {
            $finance_nature->updated_by = auth()->user()->id;
            $finance_nature->save();
        }

        return $finance_nature->refresh();
    }

    /**
     * @param FinanceNature $finance_nature
     */
    public function destroy(FinanceNature $finance_nature)
    {
        $deleted = $this->deleteById($finance_nature->id);

        if ($deleted) {
            $finance_nature->deleted_by = auth()->user()->id;
            $finance_nature->save();
        }
    }
}

