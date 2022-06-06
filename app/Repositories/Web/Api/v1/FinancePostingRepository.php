<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\FinancePosting;
use App\Repositories\BaseRepository;

class FinancePostingRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return FinancePosting::class;
    }

    /**
     * @param array $data
     *
     * @return FinancePosting
     */
    public function create(array $data) : FinancePosting
    {
        
        $posting = FinancePosting::create([
            'amount' => $data['amount'],
            'branch_id' => $data['branch_id'],
            'from_finance_account_id' => $data['from_finance_account_id'],
            'to_finance_account_id' => $data['to_finance_account_id'],
            'status' => $data['status'],
            'description' => isset($data['description']) ? $data['description'] : null,
            'posting_type' => isset($data['posting_type']) ? $data['posting_type'] : null,
            'posting_type_id' => isset($data['posting_type_id']) ? $data['posting_type_id'] : null,
            'from_actor_type' => isset($data['from_actor_type']) ? $data['from_actor_type'] : null,
            'from_actor_type_id' => isset($data['from_actor_type_id']) ? $data['from_actor_type_id'] : null,
            'to_actor_type' => isset($data['to_actor_type']) ? $data['to_actor_type'] : null,
            'to_actor_type_id' => isset($data['to_actor_type_id']) ? $data['to_actor_type_id'] : null,
            'created_by' => auth()->user()->id
        ]);

        $posting->refresh();
        
        if ($posting) {
            
            $new_posting = FinancePosting::create([
                'amount' => $data['amount'],
                'branch_id' => $data['branch_id'],
                'from_finance_account_id' => $data['to_finance_account_id'],
                'to_finance_account_id' => $data['from_finance_account_id'],
                'status' => ($data['status']== 'credit')? 'debit' : 'credit',
                'description' => isset($data['description']) ? $data['description'] : null,
                'posting_type' => isset($data['posting_type']) ? $data['posting_type'] : null,
                'posting_type_id' => isset($data['posting_type_id']) ? $data['posting_type_id'] : null,
                'posting_id' => $posting->id,
                'from_actor_type' => isset($data['from_actor_type']) ? $data['from_actor_type'] : null,
                'from_actor_type_id' => isset($data['from_actor_type_id']) ? $data['from_actor_type_id'] : null,
                'to_actor_type' => isset($data['to_actor_type']) ? $data['to_actor_type'] : null,
                'to_actor_type_id' => isset($data['to_actor_type_id']) ? $data['to_actor_type_id'] : null,
                'created_by' => auth()->user()->id
            ]);
    
            $posting->update(['posting_id' => $new_posting->id]);
        }

        return $posting->refresh();
    }
    /**
     * @param FinancePosting  $finance_advance
     * @param array $data
     *
     * @return mixed
     */
    // public function update(FinancePosting $finance_advance, array $data) : FinancePosting
    // {
    //     $finance_advance->amount = $data['amount'];
    //     $finance_advance->finance_account_id = isset($data['finance_account_id']) ? $data['finance_account_id'] : $finance_advance->finance_account_id;
    //     $finance_advance->branch_id = isset($data['branch_id']) ? $data['branch_id'] : $finance_advance->branch_id;
    //     $finance_advance->reason = isset($data['reason']) ? $data['reason'] : $finance_advance->reason;

    //     if($finance_advance->isDirty()) {
    //         $finance_advance->updated_by = auth()->user()->id;
    //         $finance_advance->save();
    //     }

    //     return $finance_advance->refresh();
    // }

    /**
     * @param FinancePosting $finance_advance
     */
    // public function destroy(FinancePosting $finance_advance)
    // {
    //     $deleted = $this->deleteById($finance_advance->id);

    //     if ($deleted) {
    //         $finance_advance->deleted_by = auth()->user()->id;
    //         $finance_advance->save();
    //     }
    // }
}

