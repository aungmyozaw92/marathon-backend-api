<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\FinanceAdvance;
use App\Repositories\BaseRepository;
use App\Repositories\Web\Api\v1\FinancePostingRepository;

class FinanceAdvanceRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return FinanceAdvance::class;
    }

    /**
     * @param array $data
     *
     * @return FinanceAdvance
     */
    public function create(array $data): FinanceAdvance
    {
        $finance_advance = FinanceAdvance::create([
            'amount' => $data['amount'],
            'branch_id' => $data['branch_id'],
            'staff_id' => isset($data['staff_id']) ? $data['staff_id'] : null,
            'from_finance_account_id' => $data['from_finance_account_id'],
            'to_finance_account_id' => $data['to_finance_account_id'],
            'reason' => isset($data['reason']) ? $data['reason'] : null,
            'status' => isset($data['status']) ? $data['status'] : 0,
            'created_by' => auth()->user()->id
        ]);

        if ($finance_advance) {
            $data['status'] = 'credit';
            $data['posting_type'] = 'FinanceAdvance';
            $data['posting_type_id'] = $finance_advance->id;
            
            $postingRepository = new FinancePostingRepository();
            $postingRepository->create($data);
        }
        if (isset($data['attachments']) && !empty($data['attachments'])) {
            $attachmentRepository = new AttachmentRepository();
            foreach ($data['attachments'] as $attachment) {
                $attachmentRepository->finance_attachment($finance_advance, 'finance_advance', 'id', $attachment);
            }
        }
        return $finance_advance;
    }

    /**
     * @param FinanceAdvance  $finance_advance
     * @param array $data
     *
     * @return mixed
     */
    public function update(FinanceAdvance $finance_advance, array $data): FinanceAdvance
    {
        $finance_advance->amount = $data['amount'];
        $finance_advance->to_finance_account_id = isset($data['to_finance_account_id']) ? $data['to_finance_account_id'] : $finance_advance->to_finance_account_id;
        $finance_advance->from_finance_account_id = isset($data['from_finance_account_id']) ? $data['from_finance_account_id'] : $finance_advance->from_finance_account_id;
        $finance_advance->branch_id = isset($data['branch_id']) ? $data['branch_id'] : $finance_advance->branch_id;
        $finance_advance->staff_id = isset($data['staff_id']) ? $data['staff_id'] : $finance_advance->staff_id;
        $finance_advance->reason = isset($data['reason']) ? $data['reason'] : $finance_advance->reason;
        $finance_advance->status = isset($data['status']) ? $data['status'] : $finance_advance->status;

        if ($finance_advance->isDirty()) {
            $finance_advance->updated_by = auth()->user()->id;
            $finance_advance->save();
        }

        return $finance_advance->refresh();
    }

    /**
     * @param FinanceAdvance  $finance_advance
     *
     * @return mixed
     */
    public function confirm(FinanceAdvance $finance_advance, array $data): FinanceAdvance
    {
        $finance_advance->status = isset($data['status']) ? $data['status'] : $finance_advance->status;

        if ($finance_advance->isDirty()) {
            $finance_advance->updated_by = auth()->user()->id;
            $finance_advance->save();

            $data['status'] = 'credit';
            $data['amount'] = $finance_advance->amount;
            $data['branch_id'] = $finance_advance->branch_id;
            $data['posting_type'] = 'FinanceAdvance';
            $data['posting_type_id'] = $finance_advance->id;
            $data['from_finance_account_id'] = $finance_advance->to_finance_account_id;
            $data['to_finance_account_id'] = $finance_advance->from_finance_account_id;
            
            $postingRepository = new FinancePostingRepository();
            $postingRepository->create($data);
        }

        return $finance_advance->refresh();
    }

    public function upload(FinanceAdvance $finance_advance, array $data): FinanceAdvance
    {
        if (isset($data['file']) && $data['file']) {
            $attachmentRepository = new AttachmentRepository();
            $attachmentRepository->finance_attachment($finance_advance, 'finance_advance', 'id', $data['file']);
        }
        return $finance_advance->refresh();
    }

    /**
     * @param FinanceAdvance $finance_advance
     */
    public function destroy(FinanceAdvance $finance_advance)
    {
        $deleted = $this->deleteById($finance_advance->id);

        if ($deleted) {
            $finance_advance->deleted_by = auth()->user()->id;
            $finance_advance->save();
        }
    }
}
