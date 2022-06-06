<?php

namespace App\Repositories\Web\Api\v1\MerchantDashboard;

use App\Models\AccountInformation;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Hash;

class AccountInformationRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return AccountInformation::class;
    }

    /**
     * @param array $data
     *
     * @return AccountInformation
     */
    public function create(array $data) : AccountInformation
    {
        $account = AccountInformation::create([
            'account_name'      => $data['account_name'],
            'account_no'        => $data['account_no'],
            'bank_id'           => $data['bank_id'],
            'resourceable_type' => 'Merchant',
            'resourceable_id'   => auth()->user()->id,
            'created_by'        => auth()->user()->id,
        ]);

        return $account;
    }

    /**
     * @param AccountInformation  $account
     * @param array $data
     *
     * @return mixed
     */
    public function update(AccountInformation $account, array $data) : AccountInformation
    {
        $account->account_name  = $data['account_name'];
        $account->account_no    = $data['account_no'];
        $account->bank_id       = $data['bank_id'];
        // $account->resourceable_type = $data['resourceable_type'];
        // $account->resourceable_id = $data['resourceable_id'];
        
        if ($account->isDirty()) {
            $account->updated_by = auth()->user()->id;
            $account->save();
        }

        return $account->refresh();
    }

    /**
     * @param AccountInformation $account
     */
    public function destroy(AccountInformation $account)
    {
        $deleted = $this->deleteById($account->id);

        if ($deleted) {
            $account->deleted_by = auth()->user()->id;
            $account->save();
        }
    }
}
