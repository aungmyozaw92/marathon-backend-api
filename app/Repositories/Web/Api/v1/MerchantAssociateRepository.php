<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Merchant;
use App\Models\ContactAssociate;
use App\Models\MerchantAssociate;
use App\Repositories\BaseRepository;
use App\Repositories\Web\Api\v1\AccountInformationRepository;

class MerchantAssociateRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return MerchantAssociate::class;
    }


    public function create(array $data): Merchant
    {
        $merchant = Merchant::find($data['merchant_id']);

        $address = getConvertedString($data['address']);

        $merchantAssociate = MerchantAssociate::create([
            'merchant_id' => $merchant->id,
            'city_id'     => $data['city_id'],
            'zone_id'     => $data['zone_id'],
            'label'       => $data['label'],
            'address'     => $address,
            'is_default'     => isset($data['is_default']) ? $data['is_default'] : 0,
            'created_by'  => $merchant->id,
        ]);

        foreach ($data["phones"] as $phone) {
            ContactAssociate::create([
                'merchant_id'           => $merchant->id,
                'merchant_associate_id' => $merchantAssociate->id,
                'type'                  => 'phone',
                'value'                 => $phone['phone']
            ]);
        }

        if (isset($data["emails"])) {
            foreach ($data["emails"] as $email) {
                ContactAssociate::create([
                    'merchant_id'            => $merchant->id,
                    'merchant_associate_id'  => $merchantAssociate->id,
                    'type'                   => 'email',
                    'value'                  => isset($email['email']) ? $email['email'] : ""
                ]);
            }
        }

        if (isset($data["account_no"])) {
            $accountInformationRepository = new AccountInformationRepository();
            $data ['resourceable_type'] = 'MerchantAssociate';
            $data ['resourceable_id'] = $merchantAssociate->id;
            $data ['merchant_associate_id'] = $merchantAssociate->id;
            $accountInformationRepository->create($data);
        }


        return $merchant;
    }

    public function update(MerchantAssociate $merchantAssociate, array $data): Merchant
    {
        $merchant = $merchantAssociate->merchant;

        if(isset($data['is_default']) && $data['is_default']){
            $merchant->merchant_associates()->update(['is_default' => 0]);
        }

        $address = getConvertedString($data['address']);

        $merchantAssociate->city_id     = $data['city_id'];
        $merchantAssociate->zone_id     = $data['zone_id'];
        $merchantAssociate->label       = $data['label'];
        $merchantAssociate->address     = $address;
        $merchantAssociate->updated_by  = $merchant->id;
        $merchantAssociate->is_default  = isset($data['is_default']) ? $data['is_default'] : $merchantAssociate->is_default;

        if ($merchantAssociate->isDirty()) {
            $merchantAssociate->save();
        }

        ContactAssociate::where('merchant_id', $merchant->id)
            ->where('merchant_associate_id', $merchantAssociate->id)
            ->delete();

        foreach ($data["phones"] as $phone) {
            if (isset($phone['id']) && isset($phone['is_delete']) && $phone['is_delete'] == true) {
                $contactAssociate = ContactAssociate::findOrFail($phone['id']);
                $contactAssociate->delete();
            } elseif (isset($phone['id'])) {
                $contactAssociate = ContactAssociate::findOrFail($phone['id']);

                $contactAssociate->value = $phone['phone'];
                $contactAssociate->save();
            } else {
                ContactAssociate::create([
                    'merchant_id'            => $merchant->id,
                    'merchant_associate_id'  => $merchantAssociate->id,
                    'type'                   => 'phone',
                    'value'                  => $phone['phone']
                ]);
            }
        }

        if (isset($data["emails"])) {
            foreach ($data["emails"] as $email) {
                if (isset($email['id']) && isset($email['is_delete']) && $email['is_delete'] == true) {
                    $contactAssociate = ContactAssociate::findOrFail($email['id']);
                    $contactAssociate->delete();
                } elseif (isset($email['id'])) {
                    $contactAssociate = ContactAssociate::findOrFail($email['id']);

                    $contactAssociate->value = isset($email['email']) ? $email['email'] : "";
                    $contactAssociate->save();
                } else {
                    ContactAssociate::create([
                        'merchant_id'            => $merchant->id,
                        'merchant_associate_id'  => $merchantAssociate->id,
                        'type'                   => 'email',
                        'value'                  => isset($email['email']) ? $email['email'] : ""
                    ]);
                }
            }
        }

        if (isset($data["account_no"])) {
            $accountInformationRepository = new AccountInformationRepository();
            $data ['resourceable_type'] = 'MerchantAssociate';
            $data ['resourceable_id'] = $merchantAssociate->id;
            $accountInformationRepository->create($data);
        }

        return $merchant->refresh();
    }

    /**
     * @param Merchant $merchant
     */
    public function destroy(MerchantAssociate $merchantAssociate)
    {
        $contactAssociate = $merchantAssociate->contact_associates;
        foreach ($contactAssociate as $d) {
            $d->delete($d->id);
        }
        $deleted = $this->deleteById($merchantAssociate->id);

        if ($deleted) {
            $merchantAssociate->deleted_by = auth()->user()->id;
            $merchantAssociate->save();
        }
    }
}
