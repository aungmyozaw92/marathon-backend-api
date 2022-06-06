<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\FinanceConfig;
use App\Repositories\BaseRepository;

class FinanceConfigRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return FinanceConfig::class;
    }

    /**
     * @param array $data
     *
     * @return FinanceConfig
     */
    public function create(array $data) : FinanceConfig
    {
        return FinanceConfig::create([
            'screen' => $data['screen'],
            'finance_account_id' => $data['from_finance_account_id'],
            'to_finance_account_id' => isset($data['to_finance_account_id']) ? $data['to_finance_account_id'] : null,
            'branch_id' => $data['branch_id'],
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param FinanceConfig  $finance_code
     * @param array $data
     *
     * @return mixed
     */
    public function update(FinanceConfig $finance_code, array $data) : FinanceConfig
    {
        $finance_code->screen = isset($data['screen']) ? $data['screen'] : $finance_code->screen;
        $finance_code->finance_account_id = isset($data['from_finance_account_id']) ? $data['from_finance_account_id'] : $finance_code->finance_account_id;
        $finance_code->branch_id = isset($data['branch_id']) ? $data['branch_id'] : $finance_code->branch_id;
        $finance_code->to_finance_account_id = isset($data['to_finance_account_id']) ? $data['to_finance_account_id'] : $finance_code->from_finance_account_id;

        if($finance_code->isDirty()) {
            $finance_code->updated_by = auth()->user()->id;
            $finance_code->save();
        }

        return $finance_code->refresh();
    }

    /**
     * @param FinanceConfig $finance_code
     */
    public function destroy(FinanceConfig $finance_code)
    {
        $deleted = $this->deleteById($finance_code->id);

        if ($deleted) {
            $finance_code->deleted_by = auth()->user()->id;
            $finance_code->save();
        }
    }
}

