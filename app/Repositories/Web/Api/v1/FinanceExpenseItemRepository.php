<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\FinanceExpenseItem;
use App\Repositories\BaseRepository;

class FinanceExpenseItemRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return FinanceExpenseItem::class;
    }

    /**
     * @param array $data
     *
     * @return FinanceExpenseItem
     */
    public function create(array $data) : FinanceExpenseItem
    {
        return FinanceExpenseItem::create([
            'description' =>  isset($data['description']) ? $data['description'] : null,
            'qty' => $data['qty'],
            'spend_at' => $data['spend_at'],
            'amount' => $data['amount'],
            'from_finance_account_id' => $data['from_finance_account_id'],
            'to_finance_account_id' => $data['to_finance_account_id'],
            'finance_expense_id' => $data['finance_expense_id'],
            'tax_amount' => isset($data['tax_amount']) ? $data['tax_amount'] : 0,
            'remark' => isset($data['remark']) ? $data['remark'] : null,
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param FinanceExpenseItem  $finance_expense_item
     * @param array $data
     *
     * @return mixed
     */
    public function update(FinanceExpenseItem $finance_expense_item, array $data) : FinanceExpenseItem
    {
        $finance_expense_item->spend_at = $data['spend_at']? $data['spend_at'] : $finance_expense_item->spend_at;;
        $finance_expense_item->description = $data['description']? $data['description'] : $finance_expense_item->description;
        $finance_expense_item->qty = isset($data['qty']) ? $data['qty'] : $finance_expense_item->qty;
        $finance_expense_item->amount = isset($data['amount']) ? $data['amount'] : $finance_expense_item->amount;
        $finance_expense_item->finance_account_id = isset($data['finance_account_id']) ? $data['finance_account_id'] : $finance_expense_item->finance_account_id;
        $finance_expense_item->finance_expense_id = isset($data['finance_expense_id']) ? $data['finance_expense_id'] : $finance_expense_item->finance_expense_id;

        if($finance_expense_item->isDirty()) {
            $finance_expense_item->updated_by = auth()->user()->id;
            $finance_expense_item->save();
        }

        return $finance_expense_item->refresh();
    }

    /**
     * @param FinanceExpenseItem $finance_expense_item
     */
    public function destroy(FinanceExpenseItem $finance_expense_item)
    {
        $deleted = $this->deleteById($finance_expense_item->id);

        if ($deleted) {
            $finance_expense_item->deleted_by = auth()->user()->id;
            $finance_expense_item->save();
        }
    }
}

