<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\FinanceCode;
use App\Repositories\BaseRepository;

class FinanceCodeRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return FinanceCode::class;
    }

    /**
     * @param array $data
     *
     * @return FinanceCode
     */
    public function create(array $data) : FinanceCode
    {
        return FinanceCode::create([
            'name' => $data['name'],
            'code' => $data['code'],
            'description' => isset($data['description']) ? $data['description'] : null,
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param FinanceCode  $finance_code
     * @param array $data
     *
     * @return mixed
     */
    public function update(FinanceCode $finance_code, array $data) : FinanceCode
    {
        $finance_code->name = $data['name'];
        $finance_code->code = isset($data['code']) ? $data['code'] : $finance_code->code;
        $finance_code->description = isset($data['description']) ? $data['description'] : $finance_code->description;

        if($finance_code->isDirty()) {
            $finance_code->updated_by = auth()->user()->id;
            $finance_code->save();
        }

        return $finance_code->refresh();
    }

    /**
     * @param FinanceCode $finance_code
     */
    public function destroy(FinanceCode $finance_code)
    {
        $deleted = $this->deleteById($finance_code->id);

        if ($deleted) {
            $finance_code->deleted_by = auth()->user()->id;
            $finance_code->save();
        }
    }
}

