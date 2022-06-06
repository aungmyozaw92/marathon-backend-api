<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\FinanceTax;
use App\Repositories\BaseRepository;

class FinanceTaxRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return FinanceTax::class;
    }

    /**
     * @param array $data
     *
     * @return FinanceTax
     */
    public function create(array $data) : FinanceTax
    {
        return FinanceTax::create([
            'name' => $data['name'],
            'amount' => $data['amount'],
            'description' => isset($data['description']) ? $data['description'] : null,
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param FinanceTax  $finance_tax
     * @param array $data
     *
     * @return mixed
     */
    public function update(FinanceTax $finance_tax, array $data) : FinanceTax
    {
        $finance_tax->name = $data['name'];
        $finance_tax->amount = isset($data['amount']) ? $data['amount'] : $finance_tax->amount;
        $finance_tax->description = isset($data['description']) ? $data['description'] : $finance_tax->description;

        if($finance_tax->isDirty()) {
            $finance_tax->updated_by = auth()->user()->id;
            $finance_tax->save();
        }

        return $finance_tax->refresh();
    }

    /**
     * @param FinanceTax $finance_tax
     */
    public function destroy(FinanceTax $finance_tax)
    {
        $deleted = $this->deleteById($finance_tax->id);

        if ($deleted) {
            $finance_tax->deleted_by = auth()->user()->id;
            $finance_tax->save();
        }
    }
}

