<?php

namespace App\Repositories\Mobile\Api\v2\Merchant;

use App\Models\Customer;
use App\Repositories\BaseRepository;
use App\Repositories\Web\Api\v1\AccountRepository;

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
		if (isset($data['address'])) {
			$address = getConvertedString($data['address']);
		}
		$name = getConvertedString($data['name']);

		$customer = Customer::create([
			'name' => $name,
			'phone' => $data['phone'],
			'other_phone' => isset($data['other_phone']) ? $data['other_phone'] : null,
			'address' => isset($data['address']) ? $address : null,
			'point' => isset($data['point']) ? $data['point'] : 0,
			'phone_confirmation_token' => Customer::generateVerificationToken(),
			'city_id' => isset($data['city_id']) ? $data['city_id'] : null,
			'zone_id' => isset($data['zone_id']) ? $data['zone_id'] : null,
			'badge_id' => isset($data['badge_id']) ? $data['badge_id'] : null,
			'latitude' => isset($data['latitude']) ? $data['latitude'] : null,
			'longitude' => isset($data['longitude']) ? $data['longitude'] : null,
			'email' => isset($data['email']) ? $data['email'] : null,
			'created_by' => 1, //auth()->user()->id,
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
		$customer->other_phone = $data['other_phone'];
		$customer->address = isset($data['address']) ? $address : $customer->address;
		$customer->point = isset($data['point']) ? $data['point'] : $customer->point;
		$customer->city_id = isset($data['city_id']) ? $data['city_id'] : $customer->city_id;
		$customer->zone_id = isset($data['zone_id']) ? $data['zone_id'] : null;
		$customer->badge_id = isset($data['badge_id']) ? $data['badge_id'] : $customer->badge_id;
		$customer->latitude = isset($data['latitude']) ? $data['latitude'] : $customer->latitude;
		$customer->longitude = isset($data['longitude']) ? $data['longitude'] : $customer->longitude;
		$customer->email = isset($data['email']) ? $data['email'] : $customer->email;

		if ($customer->isDirty()) {
			//$customer->updated_by = auth()->user()->id;
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
		$deleted = $this->deleteById($customer->id);

		if ($deleted) {
			//    $customer->deleted_by = auth()->user()->id;
			$customer->save();
		}
	}

	public function rate(Customer $customer, $type)
	{
		if ($type === 'order') {
			$order = $customer->order + 1;
			$customer->order = $order;
			$rate = (($order - $customer->return) / $order) * 100;
		}
		if ($type === 'success') {
			$success = $customer->success + 1;
			$customer->success = $success;
			$rate = (($customer->order - $customer->return) / $customer->order) * 100;
		}
		if ($type === 'return') {
			$return = $customer->return + 1;
			$customer->return = $return;
			$rate = (($customer->order - $return) / $customer->order) * 100;
		}
		$customer->rate = $rate;

		if ($customer->isDirty()) {
			// $customer->updated_by = auth()->user()->id;
			$customer->save();
		}
	}
}
