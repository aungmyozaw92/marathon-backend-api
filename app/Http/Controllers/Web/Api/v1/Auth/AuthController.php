<?php

namespace App\Http\Controllers\Web\Api\v1\Auth;

use DB;
use JWTAuth;
use Validator;
use App\Models\Staff;
use App\Models\Attachment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\City\CityResource;
use App\Http\Requests\Transaction\FileRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\Attachment\AttachmentResource;
use App\Http\Resources\Department\DepartmentResource;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        Config::set('auth.defaults.guard', 'api');
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
            $staff = DB::table('staffs')->where('username', $request->input('username'))->first();
        }

        $token = JWTAuth::attempt($credentials);

        if (!$token) {
            return response()->json(['status' => 2, 'message' => 'Wrong credentials.'], 200);
        }

        Staff::where('id', $staff->id)->update(['token' => $token]);

        return $this->respondWithToken($token);
    }

    public function profile()
    {
        $user = auth()->user()->refresh();
        return response()->json([
            'status' => 1,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'city_id' => $user->city_id,
                    'department_id' => $user->department_id,
                    'department' => DepartmentResource::make($user->department),
                    'zone_id' => $user->zone_id,
                    'city_id' => $user->city_id,
                    'city' => CityResource::make($user->city),
                    'courier_type_id' => $user->courier_type_id,
                    'profile_image' => $user->profile ?  AttachmentResource::make($user->profile) : null
                ]
            ],
        ], 200);
        // return response()->json([
        //     'status' => 1,
        //     'data' => auth('api')->user(),
        // ], 200);
    }

    public function update_profile(UpdateProfileRequest $request)
    {
        $staff = $this->guard()->user();

        $staff = $request->updateProfile($staff);

        return response()->json(['status' => 1, 'message' => 'Successfully updated!'], 200);
    }

    public function uploadProfile(FileRequest $request)
    {
        // Delete Profile When User has already profile
        if (auth()->user()->profile) {
            $date_path = auth()->user()->profile->created_at->format('F-Y');
            Storage::disk('dospace')->delete('profile/' . $date_path . '/' . auth()->user()->profile);
            $deleted = Attachment::findOrFail(auth()->user()->profile->id)->delete();
            if ($deleted) {
                auth()->user()->profile->deleted_by = auth()->user()->id;
                auth()->user()->profile->save();
            }
        }

        if ($request->hasFile('file') && $file = $request->file('file')) {
            /**
            * Check Request has File
            */
            $file_name = null;
            $folder  = 'profile';
            $date_folder = date('F-Y');
            $path = $folder.'/'.$date_folder;
            if (gettype($file) == 'string') {
                $file_name = auth()->user()->name . '_image_' . time() . '.' . 'png';
                $file_content = base64_decode($file);
            } else {
                $file_name = auth()->user()->name . '_image_' . time() . '_' . $file->getClientOriginalName();
                $file_content = file_get_contents($file);
            }
            Storage::disk('dospace')->put($path . '/' . $file_name, $file_content);
            Storage::setVisibility($path . '/' . $file_name, "public");
                
            Attachment::create([
                        'resource_type' => 'Staff',
                        'image' => $file_name,
                        'resource_id' => auth()->user()->id,
                        'note' => 'User Profile',
                        'latitude' => null,
                        'longitude' => null,
                        'is_sign' => 0,
                        'created_by' => auth()->user()->id
                    ]);

            return $this->profile();
        }
    }

    public function logout(Request $request)
    {
        // Get JWT Token from the request header key "Authorization"
        $token = $request->header('Authorization');

        // Invalidate the token
        try {
            $header_token = explode('Bearer ', $token);
            Staff::where('token', $header_token[1])->update(['token' => null]);

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

    public function clearAllTokens(Request $request)
    {
        // Get JWT Token from the request header key "Authorization"
        $token = $request->header('Authorization');
        $header_token = explode('Bearer ', $token);
        $staffs = Staff::where('token', '!=', $header_token[1])
            ->whereIn('department_id', [1, 2, 3, 4])
            ->whereNotNull('token')->get();

        foreach ($staffs as $staff) {
            $staff->token = null;
            JWTAuth::invalidate($staff->token);
            $staff->save();
        }

        return response()->json([
            'status' => 1,
            'message' => 'User tokens has been cleared.',
        ], 200);
    }

    public function check_password(Request $request)
    {
        $user = auth()->user();
        if (Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'status' => 1, 'message' => 'Success',
            ], 200);
        }
        return response()->json([
            'status' => 2, 'message' => 'Password is incorrect',
        ], 200);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    protected function respondWithToken($token)
    {
        $user = $this->guard()->user();

        return response()->json([
            'status' => 1,
            'data' => [
                'access_token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'city_id' => $user->city_id,
                    'department_id' => $user->department_id,
                    'department' => DepartmentResource::make($user->department),
                    'zone_id' => $user->zone_id,
                    'city_id' => $user->city_id,
                    'city' => CityResource::make($user->city),
                    'courier_type_id' => $user->courier_type_id,
                    'profile_image' => $user->profile ?  AttachmentResource::make($user->profile) : null
                ],
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60 * 24,
            ],
        ], 200);
    }

    public function guard()
    {
        return \Auth::Guard('api');
    }

    public function check()
    {
        return response()->json([
            'status' => 1,
            'message' => 'Authorization token is valid.',
        ], 200);
    }
}
