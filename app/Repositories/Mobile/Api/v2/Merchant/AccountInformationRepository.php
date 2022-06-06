<?php

namespace App\Repositories\Mobile\Api\v2\Merchant;

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
    public function create(array $data): AccountInformation
    {
        $merchant = auth()->user();
        if (isset($data['is_default']) && ($data['is_default']  === 1 || $data['is_default'] === "true" || $data['is_default'] === true)) {
            $this->undoDefault($merchant);
        }
        $account = AccountInformation::create([
            'account_name'      => $data['account_name'],
            'account_no'        => $data['account_no'],
            'bank_id'           => $data['bank_id'],
            'is_default'    => isset($data['is_default']) ? $data['is_default'] : 0,
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
    public function update(AccountInformation $account, array $data): AccountInformation
    {
        $merchant = auth()->user();
        if (isset($data['is_default']) && ($data['is_default']  === 1 || $data['is_default'] === "true" || $data['is_default'] === true)) {
            $this->undoDefault($merchant);
        }
        $account->account_name  = $data['account_name'];
        $account->account_no    = $data['account_no'];
        $account->bank_id       = $data['bank_id'];
        $account->is_default  = isset($data['is_default']) ? $data['is_default'] : $account->is_default;
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
    // public function undoDefault($merchant)
    // {
    //     $merchant->account_informations()->where('is_default', true)->update(['is_default' => false]);
    // }
    public function undoDefault($merchant)
    {
        $associates = $merchant->account_informations()->where('is_default', true)->get();
        foreach ($associates as $assoc) {
            $assoc->is_default = false;
            $assoc->save();
        }
    }
}
