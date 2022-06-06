<?php

namespace App\Repositories\Web\Api\v1\MerchantDashboard;

use App\Models\Customer;
use App\Models\MerchantCustomer;
use App\Repositories\BaseRepository;
use App\Repositories\Web\Api\v1\AccountRepository;
use App\Repositories\Web\Api\v1\MerchantCustomerRepository;

class CustomerRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Customer::class;
    }

    /**
     * @param array $data
     *
     * @return Customer
     */
    public function create(array $data): Customer
    {
        // dd('bi');
        if (isset($data['address'])) {
            $address = getConvertedString($data['address']);
        }
        $name = getConvertedString($data['name']);

        $customer = Customer::create([
            'name' => $name,
            'phone' => $data['phone'],
            'other_phone' => $data['other_phone'],
            'address' => isset($data['address']) ? $address:null,
            'point' => isset($data['point']) ? $data['point'] : 0,
            'phone_confirmation_token' => Customer::generateVerificationToken(),
            'city_id' => isset($data['city_id']) ? $data['city_id'] : null,
            'zone_id' => isset($data['zone_id']) ? $data['zone_id'] : null,
            'badge_id' => isset($data['badge_id']) ? $data['badge_id'] : null,
            'latitude' => isset($data['latitude']) ? $data['latitude'] : null,
            'longitude' => isset($data['longitude']) ? $data['longitude'] : null,
            'email' => isset($data['email']) ? $data['email'] : null,
            'created_by_type' => "Merchant",
            'created_by' => auth()->user()->id
        ]);
        
        $merchantCustomerRepository = new MerchantCustomerRepository();
        $merchantCustomerRepository->create([
            'merchant_id' => auth()->user()->id,
            'customer_id' => $customer->id
        ]);

        $accountRepository = new AccountRepository();
        $account = [
            'city_id' => $customer->city_id,
            'accountable_type' => 'Customer',
            'accountable_id' => $customer->id,
        ];
        $accountRepository->create($account);

        return $customer;
    }

    /**
     * @param Customer $customer
     * @param array    $data
     *
     * @return mixed
     */
    public function update(Customer $customer, array $data): Customer
    {
        if (isset($data['address'])) {
            $address = getConvertedString($data['address']);
        }
        $name = getConvertedString($data['name']);

        $customer->name = $name;
        $customer->phone = $data['phone'];
        $customer->other_phone = isset($data['other_phone']) ? $data['other_phone'] : $customer->other_phone;
        $customer->email = isset($data['email']) ? $data['email'] : $customer->email;
        $customer->address = isset($data['address']) ? $address : $customer->address;
        $customer->point = isset($data['point']) ? $data['point'] : $customer->point;
        $customer->city_id = isset($data['city_id']) ? $data['city_id'] : $customer->city_id;
        $customer->zone_id = isset($data['zone_id']) ? $data['zone_id'] : null;
        $customer->badge_id = isset($data['badge_id']) ? $data['badge_id'] : $customer->badge_id;
        $customer->latitude = isset($data['latitude']) ? $data['latitude'] : $customer->latitude;
        $customer->longitude = isset($data['longitude']) ? $data['longitude'] : $customer->longitude;
        $customer->email = isset($data['email']) ? $data['email'] : $customer->email;

        if ($customer->isDirty()) {
            $customer->updated_by_type = "Merchant";
            $customer->updated_by = auth()->user()->id;
            $customer->save();
        }
        

        if (!$customer->account) {
            $accountRepository = new AccountRepository();
            $account = [
                'city_id' => $customer->city_id,
                'accountable_type' => 'Customer',
                'accountable_id' => $customer->id,
            ];
            $accountRepository->create($account);
        }

        return $customer->refresh();
    }

    /**
     * @param Customer $customer
     */
    public function destroy(Customer $customer)
    {
        $merchant_customer = MerchantCustomer::where('merchant_id',auth()->user()->id)->where('customer_id', $customer->id)->first();
        $deleted = $this->deleteById($customer->id);

        if ($deleted) {
            $merchant_customer->destroy($merchant_customer->id);
            $customer->deleted_by_type = "Merchant";
            $customer->deleted_by = auth()->user()->id;
            $customer->save();
        }
    }
}
