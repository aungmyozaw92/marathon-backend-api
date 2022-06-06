<?php

namespace App\Http\Controllers\Mobile\Api\v2\Merchant\Auth;

use DB;
use JWTAuth;
use Validator;
use App\Models\Bank;
use App\Models\City;
use App\Models\Zone;
use App\Models\Merchant;
use App\Models\DeviceToken;
use App\Models\GlobalScale;
use App\Models\PaymentType;
use App\Services\SmsService;
use Illuminate\Http\Request;
use App\Models\FailureStatus;
use Illuminate\Http\Response;
use App\Models\ContactAssociate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Bank\BankCollection;
use App\Http\Resources\Mobile\City\CityCollection;
use App\Http\Resources\Mobile\Zone\ZoneCollection;
use App\Http\Resources\Mobile\Merchant\MerchantResource;
use App\Http\Resources\FailureStatus\FailureStatusCollection;
use App\Http\Requests\Mobile\v2\Merchant\ResetPasswordRequest;
use App\Http\Requests\Mobile\v2\Merchant\UpdateProfileRequest;
use App\Http\Requests\Mobile\v2\Merchant\ForgetPasswordRequest;
use App\Http\Requests\Mobile\v2\Merchant\ConfirmPasswordRequest;
use App\Http\Resources\Mobile\GlobalScale\GlobalScaleCollection;
use App\Http\Resources\Mobile\PaymentType\PaymentTypeCollection;
use App\Http\Resources\Mobile\v2\Merchant\Profile\ProfileResource;
use App\Models\Voucher;
use App\Services\FirebaseService;

class AuthController extends Controller
{
    protected $smsService;
	private $firebaseService;
    public function __construct(SmsService $smsService,FirebaseService $firebaseService)
    {
        $this->smsService = $smsService;
		$this->firebaseService = $firebaseService;
    }
    /**
     * Login
     */
    public function validate_phone_number(Request $request)
    {
        $request->merge([
            'phone_number' => str_replace('+95', '0', $request->get('phone_number'))
        ]);
        $credentials = $request->only('phone_number');
        $rules = [
            'phone_number' => 'required|unique:contact_associates,value,NULL,id,deleted_at,NULL|phone:MM',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 2,
                'message' => $validator->messages()
            ], 200);
        } 
        $data = $this->smsService->verifyRequest($request->get('phone_number'));
        return response()->json([
            'status' => 1, 
            'phone_number' => $request->phone_number,
            'sms_request_id' => $data
        ]);
    }

    public function verifyOtpCode(Request $request)
    {
        $credentials = $request->only('sms_request_id','otp_code');

        $rules = ['sms_request_id' => 'required|numeric',
                  'otp_code'       => 'required|numeric'];

        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 2,
                'message' => $validator->messages(),
            ], 200);
        }

        $data = $this->smsService->verify($request->get('sms_request_id'),$request->get('otp_code'));

        if($data){
            return response()->json([
                'status' => 1,
                'message' => 'Sms verification success',
            ], 200);
        }else{
            return response()->json([
                'status' => 2,
                'message' => 'Verification Fail.',
            ], 200);
        }
    }
    
    public function login(Request $request)
    {
        Config::set('auth.defaults.guard', 'merchant');
        Config::set('jwt.ttl', 43200);

        $credentials = $request->only('username', 'password');

        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 2,
                'message' => $validator->messages(),
            ], 200);
        }

        if (auth()->validate($credentials)) {
            $merchant = DB::table('merchants')->where('username', $request->input('username'))->first();
        }
        // if (!auth()->validate($credentials) && is_numeric($request->username)) {
        //     $contact = ContactAssociate::where([['type', 'phone'], ['value', $request->username]])->first();
        //     if (!$contact || !isset($contact->merchant_associate) || !isset($contact->merchant_associate->merchant)) {
        //         return response()->json(['status' => 2, 'message' => 'Wrong credentials.'], 200);
        //     }
        //     $username = $contact->merchant_associate->merchant->username;
        //     $credentials['username'] = $username;
        //     if (!auth()->validate($credentials)) {
        //         return response()->json(['status' => 2, 'message' => 'Wrong credentials.'], 200);
        //     }
        //     $merchant = DB::table('merchants')->where('username', $username)->first();
        // }

        $token = JWTAuth::attempt($credentials);

        if (!$token) {
            return response()->json(['status' => 2, 'message' => 'Wrong credentials.'], 200);
		}
        return $this->respondWithToken($token);
    }

    /**
     * Profile
     */
    public function profile()
    {
        $merchant = $this->guard()->user();
        return new ProfileResource($merchant->load([
            'staff', 'city', 'default_payment_type' , 'merchant_associates', 'merchant_associates.phones', 'merchant_associates.emails',
            'merchant_associates.city', 'merchant_associates.zone', 'account_informations', 'attachments'
        ]));
    }

    public function update_profile(UpdateProfileRequest $request)
    {
        $merchant = $this->guard()->user();
        $merchant = $request->updateProfile($merchant);
        return response()->json(['status' => 1, 'message' => 'Successfully updated!'], 200);
    }

    public function forget_password_validate(ForgetPasswordRequest $forgetPasswordRequest)
    {
        $phone_number = str_replace('+95', '0', $forgetPasswordRequest->phone_number);
        $merchants = ContactAssociate::where('type', 'phone')
            ->where('value', $phone_number)->with('merchant:id,username')->select('id', 'merchant_id')->get()->toArray();
        if (!$merchants) {
            return response()->json(['status' => 2, 'message' => 'Invalid phone number!'], 200);
        }
        $list = array_column($merchants, 'merchant');
        $merchant_usernames = array_unique(array_column($list, 'username'));
        if (!in_array($forgetPasswordRequest->username, $merchant_usernames)) {
            return response()->json(['status' => 2, 'message' => 'Username and phone number does not match!'], 200);
        }
        $merchant = Merchant::where('username', $forgetPasswordRequest->username)->first();
        if ($merchant != null) {
            return response()->json(['status' => 1, 'verified_key' => $merchant->id], 200);
        }
        return response()->json(['status' => 2, 'message' => 'Invalid username!'], 200);
    }

    public function reset_password(ResetPasswordRequest $resetPasswordRequest)
    {
        $merchant = Merchant::find($resetPasswordRequest->verified_key);
        $new_password = Hash::make($resetPasswordRequest->new_password);
        if ($merchant && $merchant->update(['password' => $new_password])) {
            return response()->json(['status' => 1, 'message' => 'Successfully reseted your password.'], 200);
        }
        return response()->json(['status' => 2, 'message' => 'Invalid username!'], 200);
    }


    public function confirm_password(ConfirmPasswordRequest $confirmPasswordRequest)
    {
        Config::set('auth.defaults.guard', 'merchant');
        $user = Merchant::where('username', auth()->user()->username)->first();
        if (Hash::check($confirmPasswordRequest->password, $user->password)) {
            return response()->json(['status' => 1, 'message' => 'Confirmed!'], 200);
        }
        return response()->json(['status' => 2, 'message' => 'Invalid password!'], 200);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
		$validator = Validator::make($request->all(), [
			'device_token_id' => [
				'required',
				function ($attribute, $value, $fail) {
					if (auth()->user()->device_tokens()->where('device_tokens.id', $value)->doesntExist()) {
						$fail($attribute . ' does not belong to you.');
					}
				},
			]
		]);
		if ($validator->fails()) {
			return response()->json([
				'status' => 2,
				'message' => $validator->messages()
			], 200);
		}
        // Get JWT Token from the request header key "Authorization"
        $token = $request->header('Authorization');

        // Invalidate the token
        try {
			auth()->user()->device_tokens()->findOrFail($request->device_token_id)
				->update(['is_active' => 0]);
            JWTAuth::invalidate($token);
            return response()->json([
                'status' => 1,
                'message' => 'User successfully logged out.',
            ], 200);
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json([
                'status' => 2,
                'message' => 'Failed to logout, please try again.',
            ], 200);
        }
    }

    /**
     * For Refresh Token
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Login Respond With Token
     */
    protected function respondWithToken($token)
    {
        $user = $this->guard()->user();
        // $attachment = $user->attachments;
        // if ($attachment->count() > 0) {
        //     $date_path = $attachment[0]->created_at->format('F-Y');
        //     $url = Storage::url('merchant' . '/' . $date_path . '/' . $attachment[0]->image);
        // } else {
        //     $url = null;
        // }
        $data = new ProfileResource($user->load([
            'staff', 'city', 'default_payment_type' , 'merchant_associates', 'merchant_associates.phones', 'merchant_associates.emails',
            'merchant_associates.city', 'merchant_associates.zone', 'account_informations', 'attachments'
        ])); 
        return response()->json([
            'status' => 1,
            'data' => [
                'access_token' => $token,
                'user' => $data,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
            ],
        ], 200);
    }
    /**
     * Authorization Guard Merchant
     */
    public function guard()
    {
        return \Auth::Guard('merchant');
    }
	/**
	 * Master data to be cached
	 */
	public function get_master_records()
    {
        $cities = City::where('is_available_d2d', 1)->with('zones')->orderBy('id', 'asc')->get();
        $zones = Zone::orderBy('id', 'asc')->get();
        $payment_types = PaymentType::whereIn('id',[1,2,3,4,9,10])->orderBy('id', 'asc')->get();
        $global_scales = GlobalScale::orderBy('id', 'asc')->get();
		$banks = Bank::orderBy('id','asc')->get();
		$failure_statuses = FailureStatus::orderBy('id','asc')->get();
        return response()->json(
            [
                'status' => 1,
                'message' => 'Success',
                'data' => [
                    'cities' => new CityCollection($cities),
                    'zones' => new ZoneCollection($zones->load(['city'])),
                    'payment_types' => new PaymentTypeCollection($payment_types),
                    'global_scales' => new GlobalScaleCollection($global_scales),
					'banks' => new BankCollection($banks),
					'failure_statuses' => new FailureStatusCollection($failure_statuses)
                ]
            ],
            Response::HTTP_OK
        );
    }

    public function save_device_token(Request $request)
    {
		// $validator = Validator::make($request->all(), ['device_token'=>'required']);
		// if ($validator->fails()) {
		// 	return response()->json([
		// 		'status' => 2,
		// 		'message' => $validator->messages()
		// 	], 200);
		// }
		$merchant = $this->guard()->user();
		$deviceToken = $request->header('DeviceToken');
		if(!$deviceToken) {
			return response()->json(['status' => 2, 'message' => 'Please,provide a device token.']);
		}
		$token = isset($merchant->device_tokens)?$merchant->device_tokens()->where('is_active', 0)->orderBy('id','asc')->first():null;
		if($token) {
			$token->update(['device_token' => $deviceToken,'is_active'=>1]);
		}else{
			$device_token = new DeviceToken(['device_token' => $deviceToken,'is_active'=>1]);
			$token = $merchant->device_tokens()->save($device_token);
		}
        return response()->json(['status'=>1,'device_token_id'=>$token->id]);
    }

    public function refresh_device_token(Request $request)
    {
		$validator = Validator::make($request->all(), ['device_token_id' => [
						'required',
						function ($attribute, $value, $fail) {
							if (auth()->user()->device_tokens()->where('device_tokens.id', $value)->doesntExist()) {
								$fail($attribute . ' does not belong to you.');
							}
						},
					]]);
		if ($validator->fails()) {
			return response()->json([
				'status' => 2,
				'message' => $validator->messages()
			], 200);
		}
		$merchant = $this->guard()->user();
		$deviceToken = $request->header('NewDeviceToken');
		if (!$deviceToken) {
			return response()->json(['status' => 2, 'message' => 'Please,provide a new device token.']);
		}
		$merchant->device_tokens()->findOrFail($request->device_token_id)
					->update(['device_token' => $deviceToken,'is_active'=>1]);
        return response()->json(['status' => 1, 'message' => 'Token was refreshed successfully.']);
	}
	
	public function test_noti(Request $request) {
		$merchant_device_tokens = auth()->user()->device_tokens()->where('is_active', 1)->pluck('device_token')->toArray();
		if(!empty($merchant_device_tokens)) {
			$request->request->add(['receiver' => auth()->user()->name]);
			$request->request->add(['device_tokens'=> $merchant_device_tokens]);
			$request->request->add(['invoice'=>'D000001']);
			$this->firebaseService->sendNotification($request->all());
			// $voucher = Voucher::find(126);
			// $sheets = $voucher->delisheets()->latest()->take(3)->get();
			// $invoice = $sheets[2]->delisheet_invoice;
			// dd($invoice);
			// $this->firebaseService->cleanNotification(['receiver'=>'shop.com.mm','invoice'=>$invoice]);
			return response()->json(['status' => 1, 'message' => 'Sent noti to you successfully.']); 
		}
		return response()->json(['status' => 2, 'message' => 'Failed. plz check your firestore document and device tokens exists or not.']);
	}
}
