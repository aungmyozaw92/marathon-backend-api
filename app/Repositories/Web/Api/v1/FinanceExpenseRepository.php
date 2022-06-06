<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\FinanceExpense;
use App\Models\FinancePosting;
use App\Repositories\BaseRepository;
use App\Repositories\Web\Api\v1\FinancePostingRepository;
use App\Repositories\Web\Api\v1\FinanceExpenseItemRepository;

class FinanceExpenseRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return FinanceExpense::class;
    }

    /**
     * @param array $data
     *
     * @return FinanceExpense
     */
    public function create(array $data): FinanceExpense
    {
        // dd($data);
        $finance_expense =  FinanceExpense::create([
            'spend_on' => $data['spend_on'],
            'total' => $data['total'],
            'branch_id' => isset($data['branch_id']) ? $data['branch_id'] : null,
            'staff_id' => isset($data['staff_id']) ? $data['staff_id'] : null,
            'is_approved' => isset($data['is_approved']) ? $data['is_approved'] : 0,
            'fn_paymant_option' => isset($data['fn_paymant_option']) ? $data['fn_paymant_option'] : null,
            'created_by' => auth()->user()->id
        ]);
        $expenseItemRepository = new FinanceExpenseItemRepository();
        $postingRepository = new FinancePostingRepository();
        if (isset($data['expense_items'])) {
            foreach ($data['expense_items'] as $item) {
                $expense_item['finance_expense_id'] = $finance_expense->id;
                $expense_item['description'] = isset($item['description']) ? $item['description'] : null;
                $expense_item['tax_amount'] = isset($item['tax_amount']) ? $item['tax_amount'] : 0;
                $expense_item['remark'] = isset($item['remark']) ? $item['remark'] : null;
                $expense_item['qty'] = isset($item['qty']) ? $item['qty'] : 0;
                $expense_item['spend_at'] = $item['spend_at'];
                $expense_item['amount'] = $item['amount'];
                $expense_item['from_finance_account_id'] = $item['from_account_id'];
                $expense_item['to_finance_account_id'] = $item['to_account_id'];
                
                $exp_item = $expenseItemRepository->create($expense_item);
                if ($exp_item) {
                    $expense_item['branch_id'] = $data['branch_id'];
                    $expense_item['status'] = 'credit';
                    $expense_item['posting_type'] = 'FinanceExpenseItem';
                    $expense_item['posting_type_id'] = $exp_item->id;
                    $expense_item['to_finance_account_id'] = $item['to_account_id'];
                    $expense_item['from_finance_account_id'] = $item['from_account_id'];
                    
                    $postingRepository->create($expense_item);
                }
            }
        }
        if (isset($data['attachments']) && !empty($data['attachments'])) {
            $attachmentRepository = new AttachmentRepository();
            foreach ($data['attachments'] as $attachment) {
                $attachmentRepository->finance_attachment($finance_expense, 'finance_expense', 'expense_invoice', $attachment);
            }
        }
        return $finance_expense;
    }

    /**
     * @param FinanceExpense  $finance_expense
     * @param array $data
     *
     * @return mixed
     */
    public function update(FinanceExpense $finance_expense, array $data): FinanceExpense
    {
        // $finance_expense->spend_at = $data['spend_at']? $data['spend_at'] : $finance_expense->spend_at;;
        $finance_expense->spend_on = isset($data['spend_on']) ? $data['spend_on'] : $finance_expense->spend_on;
        $finance_expense->total = isset($data['total']) ? $data['total'] : $finance_expense->total;
        $finance_expense->sub_total = isset($data['sub_total']) ? $data['sub_total'] : $finance_expense->sub_total;
        $finance_expense->branch_id = isset($data['branch_id']) ? $data['branch_id'] : $finance_expense->branch_id;

        if ($finance_expense->isDirty()) {
            $finance_expense->updated_by = auth()->user()->id;
            $finance_expense->save();
        }

        return $finance_expense->refresh();
    }

    public function upload(FinanceExpense $finance_expense, array $data): FinanceExpense
    {
        if (isset($data['file']) && $data['file']) {
            $attachmentRepository = new AttachmentRepository();
            $attachmentRepository->finance_attachment($finance_expense, 'finance_expense', 'expense_invoice', $data['file']);
        }

        return $finance_expense->refresh();
    }

    /**
     * @param FinanceExpense $finance_expense
     */
    public function destroy(FinanceExpense $finance_expense)
    {
        $deleted = $this->deleteById($finance_expense->id);

        if ($deleted) {
            $finance_expense->deleted_by = auth()->user()->id;
            $finance_expense->save();
        }
    }
}
