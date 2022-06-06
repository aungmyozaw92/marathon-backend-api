<?php

namespace App\Repositories\Web\Api\v1\SuperMerchant;

use App\Models\Merchant;
use App\Models\ContactAssociate;
use App\Models\MerchantAssociate;
use App\Models\AccountInformation;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Web\Api\v1\AccountRepository;
use App\Repositories\Web\Api\v1\AccountInformationRepository;

class MerchantRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Merchant::class;
    }


    public function create(array $data): Merchant
    {

        $name = getConvertedString($data['name']);
        $auth_user = auth()->user();
        $merchant = Merchant::create([
            'name'                => $name,
            'username'            => $data['username'],
            'password'            => Hash::make($data['password']),
            'is_allow_multiple_pickups' => isset($data['is_allow_multiple_pickups']) ? $data['is_allow_multiple_pickups'] : 0,
            'city_id'             => $auth_user->city_id,
            'staff_id'            => 1,
            'created_by'          => $auth_user->id,
            'super_merchant_id'          => $auth_user->id,
        ]);
        
        if (isset($data['account_informations'])) {
            foreach($data['account_informations'] as $account_information) {
                $accountInformationRepository = new AccountInformationRepository();
                $account_information ['resourceable_type'] = 'Merchant';
                $account_information ['resourceable_id'] = $merchant->id;
                $accountInformationRepository->create($account_information);
            }
        }

        if (isset($data["merchant_associates"])) {
            foreach ($data["merchant_associates"] as $branch) {
                $merchant_associate = MerchantAssociate::create([
                    'merchant_id' => $merchant->id,
                    'city_id'     => $branch['city_id'],
                    'zone_id'     => $branch['zone_id'],
                    'label'       => $branch['label'],
                    'address'     => isset($branch['address']) ? getConvertedString($branch['address']) : null,
                    'created_by'  => $auth_user->id,
                ]);

                foreach ($branch["phones"] as $phone) {
                    ContactAssociate::create([
                        'merchant_id'           => $merchant->id,
                        'merchant_associate_id' => $merchant_associate->id,
                        'type'                  => 'phone',
                        'value'                 => $phone
                    ]);
                }
                foreach ($branch["emails"] as $email) {
                    ContactAssociate::create([
                        'merchant_id'            => $merchant->id,
                        'merchant_associate_id'  => $merchant_associate->id,
                        'type'                   => 'email',
                        'value'                  => $email
                    ]);
                }
            }
        }

        // Creating account for finance
        $accountRepository = new AccountRepository();
        $account = [
            'city_id' => ($merchant->city_id) ? $merchant->city_id : $merchant->merchant_associates[0]->city_id,
            'accountable_type' => 'Merchant',
            'accountable_id' => $merchant->id,
        ];
        $accountRepository->create($account);

        return $merchant;
    }

    public function update(Merchant $merchant, array $data): Merchant
    {

        $merchant->name = isset($data['name']) ? getConvertedString($data['name']) : $merchant->name;
        //$merchant->username = isset($data['username']) ? $data['username'] : $merchant->username;
        $merchant->password = isset($data['new_password']) ? Hash::make($data['new_password']) : $merchant->password;

        if($merchant->isDirty()) {
            $merchant->updated_by = $merchant->id;
            $merchant->save();
        }

        return $merchant->refresh();
    }

    /**
     * @param Merchant $merchant
     */
    public function destroy(Merchant $merchant)
    {

        foreach ($merchant->contact_associates as $d) {
            $del = $d->delete($d->id);
            if ($del) {
                $d->deleted_by = auth()->user()->id;
                $d->save();
            }
        }
        foreach ($merchant->merchant_associates as $d) {
            $del = $d->delete($d->id);
            if ($del) {
                $d->deleted_by = auth()->user()->id;
                $d->save();
            }
        }

        $deleted = $this->deleteById($merchant->id);

        if ($deleted) {
            $merchant->deleted_by = auth()->user()->id;
            $merchant->save();
        }
    }
}
