<?php

namespace App\Repositories\Web\Api\v1;

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

        $created_by = auth()->user()->id;

        $merchant = Merchant::create([
            'name'                => $name,
            'username'            => $data['username'],
            'password'            => Hash::make($data['password']),
            'is_discount'          =>   isset($data['is_discount']) ? $data['is_discount'] : 1,
            'is_allow_multiple_pickups' =>   isset($data['is_allow_multiple_pickups']) ? $data['is_allow_multiple_pickups'] : 0,
            // 'fix_dropoff_price'   => isset($data['fix_dropoff_price']) ? $data['fix_pickup_price'] : null,
            // 'fix_delivery_price'  => isset($data['fix_delivery_price']) ? $data['fix_pickup_price'] : null,
            'city_id'             => isset($data['city_id']) ? $data['city_id'] : getBranchCityId(),
            'staff_id'            => isset($data['staff_id']) ? $data['staff_id'] : 1,
            'is_root_merchant'    => isset($data['is_root_merchant']) ? $data['is_root_merchant'] : 0,
            'static_price_same_city'     => isset($data['static_price_same_city']) ? $data['static_price_same_city'] : null,
            'static_price_diff_city'     => isset($data['static_price_diff_city']) ? $data['static_price_diff_city'] : null,
            'static_price_branch'        => isset($data['static_price_branch']) ? $data['static_price_branch'] : null,
            'is_corporate_merchant'      => isset($data['is_corporate_merchant']) ? $data['is_corporate_merchant'] : 0,
            'facebook'                   => isset($data['facebook']) ? $data['facebook'] : null,
            'facebook_url'               => isset($data['facebook_url']) ? $data['facebook_url'] : null,
            'max_withdraw_days'               => isset($data['max_withdraw_days']) ? $data['max_withdraw_days'] : 2,
            'account_code'               => isset($data['account_code']) ? $data['account_code'] : null,
            'created_by'          => $created_by,
        ]);
        
        if (isset($data['account_informations'])) {
            foreach ($data['account_informations'] as $account_information) {
                $accountInformationRepository = new AccountInformationRepository();
                $account_information['resourceable_type'] = 'Merchant';
                $account_information['resourceable_id'] = $merchant->id;
                $accountInformationRepository->create($account_information);
            }
        }

        if (isset($data["branches"])) {
            foreach ($data["branches"] as $branch) {
                $merchant_associate = MerchantAssociate::create([
                    'merchant_id' => $merchant->id,
                    'city_id'     => $branch['city_id'],
                    'zone_id'     => $branch['zone_id'],
                    'label'       => $branch['label'],
                    'is_default'  => isset($branch['is_default']) ? $branch['is_default'] : 0,
                    'address'     => isset($branch['address']) ? getConvertedString($branch['address']) : null,
                    'created_by'  => $created_by,
                ]);

                foreach ($branch["phones"] as $phone) {
                    ContactAssociate::create([
                        'merchant_id'           => $merchant->id,
                        'merchant_associate_id' => $merchant_associate->id,
                        'type'                  => 'phone',
                        'value'                 => $phone['phone']
                    ]);
                }
                foreach ($branch["emails"] as $email) {
                    ContactAssociate::create([
                        'merchant_id'            => $merchant->id,
                        'merchant_associate_id'  => $merchant_associate->id,
                        'type'                   => 'email',
                        'value'                  => $email['email']
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
        $created_by = auth()->user()->id;
        if (isset($data['name'])) {
            $name = getConvertedString($data['name']);
        }

        $merchant->name                      = isset($data['name']) ? $name : $merchant->name;
        $merchant->username                  = isset($data['username']) ? $data['username'] : $merchant->username;
        $merchant->is_discount               = isset($data['is_discount']) ? $data['is_discount'] : $merchant->is_discount;
        $merchant->is_allow_multiple_pickups = isset($data['is_allow_multiple_pickups']) ? $data['is_allow_multiple_pickups'] : $merchant->is_allow_multiple_pickups;
        // $merchant->fix_dropoff_price      = isset($data['fix_dropoff_price']) ? $data['fix_pickup_price'] : $merchant->fix_dropoff_price;
        // $merchant->fix_delivery_price     = isset($data['fix_delivery_price']) ? $data['fix_pickup_price'] : $merchant->fix_delivery_price;
        $merchant->city_id                   = isset($data['city_id']) ? $data['city_id'] : $merchant->city_id;
        $merchant->staff_id                  = isset($data['staff_id']) ? $data['staff_id'] : $merchant->staff_id;
        $merchant->is_root_merchant          = isset($data['is_root_merchant']) ? $data['is_root_merchant'] : $merchant->is_root_merchant;
        $merchant->static_price_same_city    = isset($data['static_price_same_city']) ? $data['static_price_same_city'] : $merchant->static_price_same_city;
        $merchant->static_price_diff_city    = isset($data['static_price_diff_city']) ? $data['static_price_diff_city'] : $merchant->static_price_diff_city;
        $merchant->static_price_branch       = isset($data['static_price_branch']) ? $data['static_price_branch'] :  $merchant->static_price_branch;
        $merchant->is_corporate_merchant     = isset($data['is_corporate_merchant']) ? $data['is_corporate_merchant'] : $merchant->is_corporate_merchant;
        $merchant->facebook                  = isset($data['facebook']) ? $data['facebook'] : $merchant->facebook;
        $merchant->facebook_url              = isset($data['facebook_url']) ? $data['facebook_url'] : $merchant->facebook_url;
        $merchant->max_withdraw_days              = isset($data['max_withdraw_days']) ? $data['max_withdraw_days'] : $merchant->max_withdraw_days;
        $merchant->account_code              = isset($data['account_code']) ? $data['account_code'] : $merchant->account_code;
        $merchant->password                  = isset($data['password']) ? Hash::make($data['password']) : $merchant->password;
        $merchant->updated_by                = $created_by;

        if ($merchant->isDirty()) {
            $merchant->save();
        }

        if (isset($data["branches"])) {
            foreach ($data["branches"] as $branch) {
                if (isset($branch['id']) && isset($branch['is_delete']) && $branch['is_delete'] == true) {
                    $merchantAssociate = MerchantAssociate::findOrFail($branch['id']);
                    $merchantAssociate->contact_associates()->delete();
                    $merchantAssociate->delete();
                } else {
                    if (isset($branch['id'])) {
                        if (isset($branch['address'])) {
                            $address = getConvertedString($branch['address']);
                        }

                        $merchantAssociate = MerchantAssociate::findOrFail($branch['id']);

                        $merchantAssociate->address = isset($branch['address']) ? $address : $merchantAssociate->address;
                        $merchantAssociate->label   = isset($branch['label']) ? getConvertedString($branch['label']) : $merchantAssociate->label;
                        $merchantAssociate->is_default = isset($branch['is_default']) ? $branch['is_default'] : 0;
                        $merchantAssociate->city_id = isset($branch['city_id']) ? $branch['city_id'] : $merchantAssociate->city_id;
                        $merchantAssociate->zone_id = isset($branch['zone_id']) ? $branch['zone_id'] : $merchantAssociate->zone_id;
                        $merchantAssociate->save();
                        ContactAssociate::where('merchant_id', $merchant->id)
                            ->where('merchant_associate_id', $merchantAssociate->id)
                            ->delete();

                        foreach ($branch["phones"] as $phone) {
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

                        foreach ($branch["emails"] as $email) {
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
                    } else {
                        $merchant_associate = MerchantAssociate::create([
                            'merchant_id' => $merchant->id,
                            'city_id'     => $branch['city_id'],
                            'zone_id'     => $branch['zone_id'],
                            'is_default'  => isset($branch['is_default']) ? $branch['is_default'] : 0,
                            'label'       => isset($branch['label']) ? getConvertedString($branch['label']) : $merchantAssociate->label,
                            'address'     => isset($branch['address']) ? getConvertedString($branch['address']) : null,
                            'created_by'  => $created_by,
                        ]);

                        foreach ($branch["phones"] as $phone) {
                            ContactAssociate::create([
                                'merchant_id'           => $merchant->id,
                                'merchant_associate_id' => $merchant_associate->id,
                                'type'                  => 'phone',
                                'value'                 => $phone['phone']
                            ]);
                        }
                        foreach ($branch["emails"] as $email) {
                            ContactAssociate::create([
                                'merchant_id'            => $merchant->id,
                                'merchant_associate_id'  => $merchant_associate->id,
                                'type'                   => 'email',
                                'value'                  => $email['email']
                            ]);
                        }
                    }
                }
            }
        }

        if (isset($data["account_informations"])) {
            foreach ($data["account_informations"] as $account_information) {
                if (isset($account_information['id']) && isset($account_information['is_delete']) && $account_information['is_delete'] == true) {
                    $accountInformation = AccountInformation::findOrFail($account_information['id']);
                    $accountInformation->delete();
                } else {
                    if (isset($account_information['id'])) {
                        $accountInformation = AccountInformation::findOrFail($account_information['id']);
                        $accountInformation->account_name = isset($account_information['account_name']) ? $account_information['account_name'] : $account_information->account_name;
                        $accountInformation->account_no = isset($account_information['account_no']) ? $account_information['account_no'] : $account_information->account_no;
                        $accountInformation->bank_id = isset($account_information['bank_id']) ? $account_information['bank_id'] : $account_information->bank_id;
                        $accountInformation->is_default = isset($account_information['is_default']) ? $account_information['is_default'] : 0;
                        $accountInformation->save();
                    } else {
                        $accountInformationRepository = new AccountInformationRepository();
                        $account_information ['resourceable_type'] = 'Merchant';
                        $account_information ['resourceable_id'] = $merchant->id;
                        $accountInformationRepository->create($account_information);
                    }
                }
            }
        }

        $accountRepository = new AccountRepository();
        if (!$merchant->account) {     
            $account = [
                'city_id' => ($merchant->city_id) ? $merchant->city_id : $merchant->merchant_associates[0]->city_id,
                'accountable_type' => 'Merchant',
                'accountable_id' => $merchant->id,
            ];
            $accountRepository->create($account);
        }

        if (isset($data['city_id']) && isset($merchant->account) && $merchant->account->city_id != $data['city_id']) {
            $data = [
                'city_id' => $data['city_id'],
            ];
            $accountRepository->update($merchant->account,$data);
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

    public function update_discount_status(Merchant $merchant, array $data): Merchant
    {
        $merchant->is_discount = $data['status'];
        if ($merchant->isDirty()) {
            $merchant->updated_by = auth()->user()->id;
            $merchant->save();
        }
        return $merchant->refresh();
    }

    public function restore($merchant): Merchant
    {
        $merchant->deleted_by = null;
        $merchant->deleted_at = null;
        if ($merchant->isDirty()) {
            $merchant->updated_by = auth()->user()->id;
            $merchant->save();
        }
        $merchant_associates = $merchant->merchant_associates()->withTrashed()->get();
        foreach ($merchant_associates as $merchant_associate) {
            $merchant_associate->deleted_by = null;
            $merchant_associate->deleted_at = null;
            if ($merchant_associate) {
                $merchant_associate->updated_by = auth()->user()->id;
                $merchant_associate->save();
            }
        }
        $contact_associates = $merchant->contact_associates()->withTrashed()->get();
        foreach ($contact_associates as $contact_associate) {
            $contact_associate->deleted_by = null;
            $contact_associate->deleted_at = null;
            if ($contact_associate) {
                $contact_associate->updated_by = auth()->user()->id;
                $contact_associate->save();
            }
        }
        return $merchant->refresh();
    }

    public function calculate_reward($voucher)
    {
        $merchant = $voucher->pickup->sender;
        $reward_percentage = getMerchantRewardPercentage();
        $total_extra_fee = $voucher->transaction_fee + $voucher->insurance_fee;
        if ($voucher->total_coupon_amount > 0) {
            $total_delivery_amount = $voucher->total_delivery_amount - $voucher->total_coupon_amount;
        } else {
            $total_delivery_amount = $voucher->discount_type == "extra" ?
                $voucher->total_delivery_amount + $voucher->total_discount_amount : $voucher->total_delivery_amount - $voucher->total_discount_amount;
        }
        $reward_point = ($reward_percentage/100) * ($total_delivery_amount + $total_extra_fee);
        $merchant->rewards += $reward_point ;
        if ($merchant->isDirty()) {
            $merchant->save();
        }
    }
}
