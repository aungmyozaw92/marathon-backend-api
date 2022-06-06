<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\FinanceTableOfAuthority;
use App\Repositories\BaseRepository;

class FinanceTableOfAuthorityRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return FinanceTableOfAuthority::class;
    }

    /**
     * @param array $data
     *
     * @return FinanceTableOfAuthority
     */
    public function create(array $data) : FinanceTableOfAuthority
    {
        return FinanceTableOfAuthority::create([
            'petty_amount' => $data['petty_amount'],
            'expense_amount' => isset($data['expense_amount']) ? $data['expense_amount'] : null,
            'advance_amount' => isset($data['advance_amount']) ? $data['advance_amount'] : null,
            'staff_id' => isset($data['staff_id']) ? $data['staff_id'] : null,
            'manager_id' => isset($data['manager_id']) ? $data['manager_id'] : null,
            'is_need_approve' => isset($data['is_need_approve']) ? $data['is_need_approve'] : 0,
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param FinanceTableOfAuthority  $finance_toa
     * @param array $data
     *
     * @return mixed
     */
    public function update(FinanceTableOfAuthority $finance_toa, array $data) : FinanceTableOfAuthority
    {
        $finance_toa->petty_amount = $data['petty_amount'];
        $finance_toa->expense_amount = isset($data['expense_amount']) ? $data['expense_amount'] : $finance_toa->expense_amount;
        $finance_toa->advance_amount = isset($data['advance_amount']) ? $data['advance_amount'] : $finance_toa->advance_amount;
        $finance_toa->staff_id = isset($data['staff_id']) ? $data['staff_id'] : $finance_toa->staff_id;
        $finance_toa->manager_id = isset($data['manager_id']) ? $data['manager_id'] : $finance_toa->manager_id;
        $finance_toa->is_need_approve = isset($data['is_need_approve']) ? $data['is_need_approve'] : $finance_toa->is_need_approve;
        
        if($finance_toa->isDirty()) {
            $finance_toa->updated_by = auth()->user()->id;
            $finance_toa->save();
        }

        return $finance_toa->refresh();
    }

    /**
     * @param FinanceTableOfAuthority $finance_toa
     */
    public function destroy(FinanceTableOfAuthority $finance_toa)
    {
        $deleted = $this->deleteById($finance_toa->id);

        if ($deleted) {
            $finance_toa->deleted_by = auth()->user()->id;
            $finance_toa->save();
        }
    }
}

