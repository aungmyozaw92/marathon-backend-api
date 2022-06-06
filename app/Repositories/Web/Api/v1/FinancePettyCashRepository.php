<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\FinancePettyCash;
use App\Models\FinancePosting;
use App\Repositories\BaseRepository;
use App\Repositories\Web\Api\v1\FinancePostingRepository;
use App\Repositories\Web\Api\v1\FinancePettyCashItemRepository;

class FinancePettyCashRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return FinancePettyCash::class;
    }

    /**
     * @param array $data
     *
     * @return FinancePettyCash
     */
    public function create(array $data): FinancePettyCash
    {
        // dd($data);
        $finance_petty_cash =  FinancePettyCash::create([
            'spend_on' => $data['spend_on'],
            'total' => $data['total'],
            'branch_id' => isset($data['branch_id']) ? $data['branch_id'] : null,
            'staff_id' => isset($data['staff_id']) ? $data['staff_id'] : null,
            'fn_paymant_option' => isset($data['fn_paymant_option']) ? $data['fn_paymant_option'] : null,
            'created_by' => auth()->user()->id
        ]);

        $petty_cashItemRepository = new FinancePettyCashItemRepository();
        $postingRepository = new FinancePostingRepository();
        if (isset($data['petty_cash_items'])) {
            foreach ($data['petty_cash_items'] as $item) {
                $petty_cash_item['finance_petty_cash_id'] = $finance_petty_cash->id;
                $petty_cash_item['description'] = isset($item['description']) ? $item['description'] : null;
                $petty_cash_item['remark'] = isset($item['remark']) ? $item['remark'] : null;
                $petty_cash_item['tax_amount'] = isset($item['tax_amount']) ? $item['tax_amount'] : 0;
                $petty_cash_item['remark'] = isset($item['remark']) ? $item['remark'] : null;
                $petty_cash_item['spend_at'] = $item['spend_at'];
                $petty_cash_item['amount'] = $item['amount'];
                $petty_cash_item['from_finance_account_id'] = $item['from_account_id'];
                $petty_cash_item['to_finance_account_id'] = $item['to_account_id'];
                
                $cash_item = $petty_cashItemRepository->create($petty_cash_item);
                if ($cash_item) {
                    $petty_cash_item['branch_id'] = isset($data['branch_id']) ? $data['branch_id'] : null;
                    $petty_cash_item['status'] = 'credit';
                    $petty_cash_item['posting_type'] = 'FinancePettyCashItem';
                    $petty_cash_item['posting_type_id'] = $cash_item->id;
                    $petty_cash_item['to_finance_account_id'] = $item['to_account_id'];
                    $petty_cash_item['from_finance_account_id'] = $item['from_account_id'];
                    
                    $postingRepository->create($petty_cash_item);
                }
            }
        }
        if (isset($data['attachments']) && !empty($data['attachments'])) {
            $attachmentRepository = new AttachmentRepository();
            foreach ($data['attachments'] as $attachment) {
                $attachmentRepository->finance_attachment($finance_petty_cash, 'finance_petty_cash', 'invoice_no', $attachment);
            }
        }
        return $finance_petty_cash;
    }

    /**
     * @param FinancePettyCash  $finance_expense
     * @param array $data
     *
     * @return mixed
     */
    public function update(FinancePettyCash $finance_expense, array $data): FinancePettyCash
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

    public function upload(FinancePettyCash $finance_expense, array $data): FinancePettyCash
    {
        if (isset($data['file']) && $data['file']) {
            $attachmentRepository = new AttachmentRepository();
            $attachmentRepository->finance_attachment($finance_expense, 'finance_expense', 'expense_invoice', $data['file']);
        }

        return $finance_expense->refresh();
    }

    /**
     * @param FinancePettyCash $finance_expense
     */
    public function destroy(FinancePettyCash $finance_expense)
    {
        $deleted = $this->deleteById($finance_expense->id);

        if ($deleted) {
            $finance_expense->deleted_by = auth()->user()->id;
            $finance_expense->save();
        }
    }
}
