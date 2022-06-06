<?php

namespace App\Repositories\Web\Api\v1\SuperMerchant;

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


    public function create_merchant_associate($merchant, array $data)
    {
        $auth_user = auth()->user();

        $merchant_associate = MerchantAssociate::create([
            'merchant_id' => $merchant->id,
            'city_id'     => $data['city_id'],
            'zone_id'     => $data['zone_id'],
            'label'       => $data['label'],
            'address'     => isset($data['address']) ? getConvertedString($data['address']) : null,
            'created_by'  => $auth_user->id,
        ]);

        foreach ($data["phones"] as $phone) {
            ContactAssociate::create([
                'merchant_id'           => $merchant->id,
                'merchant_associate_id' => $merchant_associate->id,
                'type'                  => 'phone',
                'value'                 => $phone
            ]);
        }
        foreach ($data["emails"] as $email) {
            ContactAssociate::create([
                'merchant_id'            => $merchant->id,
                'merchant_associate_id'  => $merchant_associate->id,
                'type'                   => 'email',
                'value'                  => $email
            ]);
        }

        return $merchant;
    }

    public function update_merchant_associate(Merchant $merchant, MerchantAssociate $merchant_associate, array $data): Merchant
    {
        $merchant_associate->city_id = isset($data['city_id']) ? $data['city_id'] : $merchant_associate->city_id;
        $merchant_associate->zone_id = isset($data['zone_id']) ? $data['zone_id'] : $merchant_associate->zone_id;
        $merchant_associate->label = isset($data['label']) ? $data['label'] : $merchant_associate->label;
        $merchant_associate->address = isset($data['address']) ? getConvertedString($data['address']) : $merchant_associate->address;
   
        if (isset($data['phones']) && $data['phones']) {
            $ids = $merchant_associate->contact_associates->where('type', 'phone')->pluck('id');
           
            ContactAssociate::destroy($ids);
            foreach ($data["phones"] as $phone) {
                ContactAssociate::create([
                    'merchant_id'           => $merchant->id,
                    'merchant_associate_id' => $merchant_associate->id,
                    'type'                  => 'phone',
                    'value'                 => $phone
                ]);
            }
        }

        if (isset($data['emails']) && $data['emails']) {
            $ids = $merchant_associate->contact_associates->where('type', 'email')->pluck('id');
            ContactAssociate::destroy($ids);
            foreach ($data["emails"] as $email) {
                ContactAssociate::create([
                    'merchant_id'            => $merchant->id,
                    'merchant_associate_id'  => $merchant_associate->id,
                    'type'                   => 'email',
                    'value'                  => $email
                ]);
            }
        }

        if($merchant_associate->isDirty()) {
            $merchant_associate->updated_by = auth()->user()->id;
            $merchant_associate->save();
        }

        return $merchant->refresh();
    }

    /**
     * @param Merchant $merchant
     */
    public function destroy(MerchantAssociate $merchant_associate)
    {
        foreach ($merchant_associate->contact_associates as $d) {
            $del = $d->delete($d->id);
            if ($del) {
                $d->deleted_by = auth()->user()->id;
                $d->save();
            }
        }
        $deleted = $this->deleteById($merchant_associate->id);

        if ($deleted) {
            $merchant_associate->deleted_by = auth()->user()->id;
            $merchant_associate->save();
        }
    }
}
