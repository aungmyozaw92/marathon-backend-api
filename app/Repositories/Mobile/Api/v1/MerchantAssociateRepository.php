<?php

namespace App\Repositories\Mobile\Api\v1;

use App\Models\Merchant;
use App\Models\ContactAssociate;
use App\Models\MerchantAssociate;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Hash;

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
        $merchant = auth()->user();

        foreach ($data as $merchant_associate) {
            if (isset($merchant_associate['address'])) {
                $address = getConvertedString($merchant_associate['address']);
            }

            $merchantAssociate = MerchantAssociate::create([
                'merchant_id' => $merchant->id,
                'city_id'     => $merchant_associate['city_id'],
                'zone_id' => $merchant_associate['zone_id'],
                'label'       => $merchant_associate['label'],
                'address'     => isset($merchant_associate['address']) ? $address : null,
                'created_by'  => $merchant->id,
            ]);

            foreach ($merchant_associate["phones"] as $phone) {
                ContactAssociate::create([
                    'merchant_id'           => $merchant->id,
                    'merchant_associate_id' => $merchantAssociate->id,
                    'type'                  => 'phone',
                    'value'                 => $phone['phone']
                ]);
            }

            foreach ($merchant_associate["emails"] as $email) {
                ContactAssociate::create([
                    'merchant_id'            => $merchant->id,
                    'merchant_associate_id'  => $merchantAssociate->id,
                    'type'                   => 'email',
                    'value'                  => $email['email']
                ]);
            }
        }

        return $merchant;
    }

    public function update(MerchantAssociate $merchantAssociate, array $data): Merchant
    {
        if (isset($data['address'])) {
            $address = getConvertedString($data['address']);
        }

        $merchant = auth()->user();

        $merchantAssociate->city_id     = $data['city_id'];
        $merchantAssociate->zone_id = $data['zone_id'];
        $merchantAssociate->label       = $data['label'];
        $merchantAssociate->address     = isset($data['address']) ? $address : null;
        $merchantAssociate->updated_by  = $merchant->id;

        if ($merchantAssociate->isDirty()) {
            $merchantAssociate->save();
        }

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

        foreach ($data["emails"] as $email) {
            if (isset($email['id']) && isset($email['is_delete']) && $email['is_delete'] == true) {
                $contactAssociate = ContactAssociate::findOrFail($email['id']);
                $contactAssociate->delete();
            } elseif (isset($email['id'])) {
                $contactAssociate = ContactAssociate::findOrFail($email['id']);

                $contactAssociate->value = $email['email'];
                $contactAssociate->save();
            } else {
                ContactAssociate::create([
                    'merchant_id'            => $merchant->id,
                    'merchant_associate_id'  => $merchantAssociate->id,
                    'type'                   => 'email',
                    'value'                  => $email['email']
                ]);
            }
        }

        return $merchant->refresh();
    }

    /**
     * @param Merchant $merchant
     */
    public function destroy(MerchantAssociate $merchantAssociate)
    {
        $deleted = $this->deleteById($merchantAssociate->id);

        if ($deleted) {
            $merchantAssociate->deleted_by = auth()->user()->id;
            $merchantAssociate->save();
        }
    }
}
