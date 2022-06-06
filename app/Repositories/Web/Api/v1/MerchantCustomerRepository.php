<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\MerchantCustomer;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Hash;

class MerchantCustomerRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return MerchantCustomer::class;
    }

    /**
     * @param array $data
     *
     * @return MerchantCustomer
     */
    public function create(array $data) : MerchantCustomer
    {
        $merchant_customer = MerchantCustomer::create([
            'merchant_id'          => $data['merchant_id'],
            'customer_id'          => $data['customer_id'],
        ]);

        return $merchant_customer;
    }

    /**
     * @param MerchantCustomer  $merchant_customer
     * @param array $data
     *
     * @return mixed
     */
    public function update(MerchantCustomer $merchant_customer, array $data) : MerchantCustomer
    {
        $merchant_customer->merchant_id = isset($data['merchant_id']) ? $data['merchant_id'] : $merchant_customer->merchant_id ;
        $merchant_customer->customer_id = isset($data['customer_id']) ? $data['customer_id']: $merchant_customer->customer_id;
        


        if ($merchant_customer->isDirty()) {
            $merchant_customer->updated_by = auth()->user()->id;
            $merchant_customer->save();
        }

        return $merchant_customer->refresh();
    }

}
