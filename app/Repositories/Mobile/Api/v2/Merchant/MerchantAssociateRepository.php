<?php

namespace App\Repositories\Mobile\Api\v2\Merchant;

use App\Models\Merchant;
use App\Models\ContactAssociate;
use App\Models\MerchantAssociate;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;

class MerchantAssociateRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return MerchantAssociate::class;
    }


    public function create(array $data)
    {
        $merchant = auth()->user();
        if (isset($data['address'])) {
            $address = getConvertedString($data['address']);
            $label = getConvertedString($data['label']);
		}
        if (isset($data['is_default']) && ($data['is_default']  === 1 || $data['is_default'] === "true" || $data['is_default'] === true)) {
            $this->undoDefault($merchant);
        }
        $data['phone'] = str_replace('+95', '0', $data['phone']);
        $unique_with_others = ContactAssociate::where([['type', 'phone'], ['value', $data['phone']]])->where('merchant_id', '<>', $merchant->id)->first();
        if ($unique_with_others) return response()->json(['status' => 2, 'message' => 'Phone number is already used in another merchant']);
        $merchantAssociate = MerchantAssociate::create([
            'merchant_id'   => $merchant->id,
            'label'         => isset($data['label']) ? $label : null,
            'address'       => isset($data['address']) ? $address : null,
            'city_id'       => $data['city_id'],
            'zone_id'       => $data['zone_id'],
            'is_default'    => isset($data['is_default']) ? $data['is_default'] : 0,
            'created_by'    => $merchant->id,
        ]);
        ContactAssociate::create([
            'merchant_id'            => $merchant->id,
            'merchant_associate_id'  => $merchantAssociate->id,
            'type'                   => 'phone',
            'value'                  => $data['phone']
        ]);
        $merchantAssociate->refresh();
        return response()->json(['status' => 1, 'message' => 'Successfully Created!'], Response::HTTP_OK);
    }

    public function update(MerchantAssociate $merchantAssociate, array $data)
    {
        if (isset($data['address'])) {
            $address = getConvertedString($data['address']);
            $label = getConvertedString($data['label']);
        }
        $merchant = auth()->user();
        if (isset($data['is_default']) && ($data['is_default']  === 1 || $data['is_default'] === "true" || $data['is_default'] === true)) {
            $this->undoDefault($merchant);
        }
        $data['phone'] = str_replace('+95', '0', $data['phone']);
        $unique_with_others = ContactAssociate::where([['type', 'phone'], ['value', $data['phone']]])->where('merchant_id', '<>', $merchant->id)->first();
        if ($unique_with_others) return response()->json(['status' => 2, 'message' => 'Phone number is already used in another merchant.Please contact to CustomerService!']);
        $merchantAssociate->label       = isset($data['label']) ? $label : $merchantAssociate->label;
        $merchantAssociate->address     = isset($data['address']) ? $address : $merchantAssociate->address;
        $merchantAssociate->city_id     = $data['city_id'];
        $merchantAssociate->zone_id     = $data['zone_id'];
        $merchantAssociate->is_default  = isset($data['is_default']) ? $data['is_default'] : $merchantAssociate->is_default;
        $merchantAssociate->updated_by  = $merchant->id;
        if ($merchantAssociate->isDirty()) {
            $merchantAssociate->save();
        }
        $contactAssociate = ContactAssociate::where('merchant_associate_id', $merchantAssociate->id)
            ->orderBy('id', 'asc')->first()->update(['value' => $data['phone']]);
        // $contactAssociate->value = $data['phone']['number'];
        // $contactAssociate->save();
        $merchantAssociate->refresh();
        return response()->json(['status' => 1, 'message' => 'Successfully Updated!'], Response::HTTP_OK);
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

    public function undoDefault($merchant)
    {
        $associates = $merchant->merchant_associates()->where('is_default', true)->get();
        foreach ($associates as $assoc) {
            $assoc->is_default = false;
            $assoc->save();
        }
    }
}
