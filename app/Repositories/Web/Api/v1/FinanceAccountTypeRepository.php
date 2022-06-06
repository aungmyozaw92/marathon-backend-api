<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\FinanceAccountType;
use App\Repositories\BaseRepository;

class FinanceAccountTypeRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return FinanceAccountType::class;
    }

    /**
     * @param array $data
     *
     * @return FinanceAccountType
     */
    public function create(array $data) : FinanceAccountType
    {
        return FinanceAccountType::create([
            'name' => $data['name'],
            'description' => isset($data['description']) ? $data['description'] : null,
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param FinanceAccountType  $account_type
     * @param array $data
     *
     * @return mixed
     */
    public function update(FinanceAccountType $account_type, array $data) : FinanceAccountType
    {
        $account_type->name = $data['name'];
        $account_type->description = isset($data['description']) ? $data['description'] : $account_type->description;

        if($account_type->isDirty()) {
            $account_type->updated_by = auth()->user()->id;
            $account_type->save();
        }

        return $account_type->refresh();
    }

    /**
     * @param FinanceAccountType $account_type
     */
    public function destroy(FinanceAccountType $account_type)
    {
        $deleted = $this->deleteById($account_type->id);

        if ($deleted) {
            $account_type->deleted_by = auth()->user()->id;
            $account_type->save();
        }
    }
}

