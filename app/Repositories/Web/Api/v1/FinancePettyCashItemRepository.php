<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\FinancePettyCashItem;
use App\Repositories\BaseRepository;

class FinancePettyCashItemRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return FinancePettyCashItem::class;
    }

    /**
     * @param array $data
     *
     * @return FinancePettyCashItem
     */
    public function create(array $data) : FinancePettyCashItem
    {
        return FinancePettyCashItem::create([
            'description' =>  isset($data['description']) ? $data['description'] : null,
            // 'qty' => $data['qty'],
            'spend_at' => $data['spend_at'],
            'amount' => $data['amount'],
            'from_finance_account_id' => $data['from_finance_account_id'],
            'to_finance_account_id' => $data['to_finance_account_id'],
            'finance_petty_cash_id' => $data['finance_petty_cash_id'],
            'tax_amount' => isset($data['tax_amount']) ? $data['tax_amount'] : 0,
            'remark' => isset($data['remark']) ? $data['remark'] : null,
            'created_by' => auth()->user()->id
        ]);
    }
    /**
     * @param FinancePettyCashItem  $finance_petty_cash_item
     * @param array $data
     *
     * @return mixed
     */
    public function update(FinancePettyCashItem $finance_petty_cash_item, array $data) : FinancePettyCashItem
    {
        $finance_petty_cash_item->spend_at = $data['spend_at']? $data['spend_at'] : $finance_petty_cash_item->spend_at;;
        $finance_petty_cash_item->description = $data['description']? $data['description'] : $finance_petty_cash_item->description;
        $finance_petty_cash_item->remark = $data['remark']? $data['remark'] : $finance_petty_cash_item->remark;
        $finance_petty_cash_item->tax_amount = isset($data['tax_amount']) ? $data['tax_amount'] : $finance_petty_cash_item->tax_amount;
        $finance_petty_cash_item->amount = isset($data['amount']) ? $data['amount'] : $finance_petty_cash_item->amount;
        $finance_petty_cash_item->from_finance_account_id = isset($data['from_finance_account_id']) ? $data['from_finance_account_id'] : $finance_petty_cash_item->from_finance_account_id;
        $finance_petty_cash_item->to_finance_account_id = isset($data['to_finance_account_id']) ? $data['to_finance_account_id'] : $finance_petty_cash_item->to_finance_account_id;
        $finance_petty_cash_item->finance_petty_cash_id = isset($data['finance_petty_cash_id']) ? $data['finance_petty_cash_id'] : $finance_petty_cash_item->finance_petty_cash_id;

        if($finance_petty_cash_item->isDirty()) {
            $finance_petty_cash_item->updated_by = auth()->user()->id;
            $finance_petty_cash_item->save();
        }

        return $finance_petty_cash_item->refresh();
    }

    /**
     * @param FinancePettyCashItem $finance_petty_cash_item
     */
    public function destroy(FinancePettyCashItem $finance_petty_cash_item)
    {
        $deleted = $this->deleteById($finance_petty_cash_item->id);

        if ($deleted) {
            $finance_petty_cash_item->deleted_by = auth()->user()->id;
            $finance_petty_cash_item->save();
        }
    }
}

