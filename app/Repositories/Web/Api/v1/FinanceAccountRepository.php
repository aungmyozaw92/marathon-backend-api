<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\FinanceAccount;
use App\Repositories\BaseRepository;

class FinanceAccountRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return FinanceAccount::class;
    }

    /**
     * @param array $data
     *
     * @return FinanceAccount
     */
    public function create(array $data) : FinanceAccount
    {
        return FinanceAccount::create([
            'name' => $data['name'],
            'code' => $data['code'],
            'description' => isset($data['description']) ? $data['description'] : null,
            'finance_nature_id' => $data['finance_nature_id'],
            'finance_master_type_id' => $data['finance_master_type_id'],
            'finance_account_type_id' => $data['finance_account_type_id'],
            'finance_group_id' => $data['finance_group_id'],
            'branch_id' => isset($data['branch_id']) ? $data['branch_id'] : null,
            'finance_tax_id' => $data['finance_tax_id'],
            'finance_code_id' => $data['finance_code_id'],
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param FinanceAccount  $finance_account
     * @param array $data
     *
     * @return mixed
     */
    public function update(FinanceAccount $finance_account, array $data) : FinanceAccount
    {
        $finance_account->name = $data['name'];
        $finance_account->code = isset($data['code']) ? $data['code'] : $finance_account->code;
        $finance_account->description = isset($data['description']) ? $data['description'] : $finance_account->description;
        $finance_account->finance_nature_id = $data['finance_nature_id'];
        $finance_account->finance_master_type_id = $data['finance_master_type_id'];
        $finance_account->finance_account_type_id = $data['finance_account_type_id'];
        $finance_account->finance_group_id = $data['finance_group_id'];
        $finance_account->branch_id = isset($data['branch_id']) ? $data['branch_id'] : null;
        $finance_account->finance_tax_id = $data['finance_tax_id'];
        $finance_account->finance_code_id = $data['finance_code_id'];

        if($finance_account->isDirty()) {
            $finance_account->updated_by = auth()->user()->id;
            $finance_account->save();
        }

        return $finance_account->refresh();
    }

    /**
     * @param FinanceAccount $finance_account
     */
    public function destroy(FinanceAccount $finance_account)
    {
        $deleted = $this->deleteById($finance_account->id);

        if ($deleted) {
            $finance_account->deleted_by = auth()->user()->id;
            $finance_account->save();
        }
    }
}

