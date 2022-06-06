<?php

namespace App\Repositories\Mobile\Api\v2\Merchant;

use App\Models\Merchant;
use App\Models\ContactAssociate;
use App\Models\MerchantAssociate;
use App\Models\Staff;
use App\Repositories\BaseRepository;
use App\Repositories\Web\Api\v1\AccountRepository;
use Illuminate\Support\Facades\Hash;
use DB;
use JWTAuth;
use Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Response;

class MerchantRepository extends BaseRepository
{
    public function model()
    {
        return Merchant::class;
    }
    public function create(array $data)
    {
        $name = getConvertedString($data['name']);
        // $staff = Staff::where('department_id', 4)->where('role_id', 1)->first();
        $existedCity  = in_array($data['city_id'], [64, 49, 35]);
        if ($existedCity) {
            $staffs = Staff::where('city_id', $data['city_id'])->where('department_id', 4)->where('role_id', 4)->pluck('id')->toArray();
            $staff_key = array_rand($staffs);
            $staff_id =  $staffs[$staff_key];
        } else {
            // $staffs = Staff::where('city_id', 64)->where('department_id', 4)->where('role_id', 4)->pluck('id')->toArray();
            // $staff = Staff::where('city_id', 64)->where('department_id', 4)->where('role_id', 1)->first();
            $staff = Staff::find(1);
            $staff_id = $staff->id;
        }

        $merchant = Merchant::create([
            'name'                => $name,
            'username'            => $data['username'],
            'password'            => Hash::make($data['password']),
            'is_discount'          =>   isset($data['is_discount']) ? $data['is_discount'] : 1,
            'is_allow_multiple_pickups' =>   isset($data['is_allow_multiple_pickups']) ? $data['is_allow_multiple_pickups'] : 0,
            'city_id'             => isset($data['city_id']) ? $data['city_id'] : getBranchCityId(),
            'staff_id'            => isset($data['staff_id']) ? $data['staff_id'] : $staff_id,
            'is_root_merchant'    => isset($data['is_root_merchant']) ? $data['is_root_merchant'] : 0,
            'static_price_same_city'     => isset($data['static_price_same_city']) ? $data['static_price_same_city'] : null,
            'static_price_diff_city'     => isset($data['static_price_diff_city']) ? $data['static_price_diff_city'] : null,
            'static_price_branch'        => isset($data['static_price_branch']) ? $data['static_price_branch'] : null,
            'is_corporate_merchant'      => isset($data['is_corporate_merchant']) ? $data['is_corporate_merchant'] : 0,
            'facebook'                   => isset($data['facebook']) ? $data['facebook'] : null,
            'facebook_url'               => isset($data['facebook_url']) ? $data['facebook_url'] : null
        ]);
        // $merchant->created_by = $merchant->id;
        // $merchant->save();
        $merchant->update(['created_by' => $merchant->id]);
        $merchant_associate = MerchantAssociate::create([
            'merchant_id' => $merchant->id,
            'city_id'     => $data['city_id'],
            'zone_id'     => $data['zone_id'],
            'label'       => isset($data['branch_name']) ? $data['branch_name'] : "Main Branch",
            'is_default'  => isset($data['is_default']) ? $data['is_default'] : 1,
            'address'     => isset($data['address']) ? getConvertedString($data['address']) : null,
            'created_by'  => $merchant->created_by,
        ]);
        foreach ($data["phones"] as $phone) {
            $phone = str_replace('+95', '0', $phone);
            ContactAssociate::create([
                'merchant_id'           => $merchant->id,
                'merchant_associate_id' => $merchant_associate->id,
                'type'                  => 'phone',
                'value'                 => $phone['phone']
            ]);
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
        $merchant->refresh();
        return $this->directLogin($merchant, $data['password']);
    }
    public function directLogin($user, $password)
    {
        Config::set('auth.defaults.guard', 'merchant');
        Config::set('jwt.ttl', 43200);
        $token = JWTAuth::attempt(['username' => $user->username, 'password' => $password]);
		$attachment = $user->attachments;
		if ($attachment->count() > 0) {
			$date_path = $attachment[0]->created_at->format('F-Y');
			$url = Storage::url('merchant' . '/' . $date_path . '/' . $attachment[0]->image);
		} else {
			$url = null;
		}
		$response = [
                'access_token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => getConvertedString($user->name),
                    'username' => $user->username,
                    'balance' => $user->account ? $user->account->balance : 0,
                    'city_id' => $user->city_id,
                    'is_allow_multiple_pickups' => $user->is_allow_multiple_pickups,
                    'is_discount' => $user->is_discount,
                    'branches' => $user->merchant_associates,
                    'staff' => $user->staff,
                    'image_url' => $url
                ],
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60
		];
        return $response;
    }
    public  function set_default(Merchant $merchant, array $data)
    {
        if ($data['default_name'] === 'branch') {
            $new_default =  $merchant->merchant_associates()->find($data['default_value']);
            if (!$new_default) return response()->json(['status' => 5, 'message' => 'Selected Branch Not Match!'], Response::HTTP_OK);;
            $this->undoDefault($merchant->merchant_associates());
            $new_default->is_default = 1;
            $new_default->save();
            return response()->json(['status' => 1, 'message' => 'Successfully Set Default!'], Response::HTTP_OK);
        }
        if ($data['default_name'] === 'bank') {
            if($data['default_value'] ==0){
                $merchant->account_informations()->update(['is_default'=>false]);
                return response()->json(['status' => 1, 'message' => 'Successfully Set Default to Cash!'], Response::HTTP_OK);
            }
            $new_default =  $merchant->account_informations()->find($data['default_value']);
            if (!$new_default) return response()->json(['status' => 5, 'message' => 'Selected Bank Not Match!'], Response::HTTP_OK);;
            // $old_default = $merchant->account_informations()->where('is_default', true)->update(['is_default' => false]);
            $this->undoDefault($merchant->account_informations());
            $new_default->is_default = 1;
            $new_default->save();
            return response()->json(['status' => 1, 'message' => 'Successfully Set Default!'], Response::HTTP_OK);
        } else {
            $merchant->default_payment_type_id = $data['default_value'];
            $merchant->save();
            return response()->json(['status' => 1, 'message' => 'Successfully Set Default!'], Response::HTTP_OK);
        }
    }
    public function undoDefault($associate)
    {
        $associates = $associate->where('is_default', true)->get();
        foreach ($associates as $assoc) {
            $assoc->is_default = false;
            $assoc->save();
        }
    }
}
