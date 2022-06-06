<?php

namespace App\Http\Controllers\Mobile\Api\v2\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\v2\Merchant\CreateMerchantRequest;
use App\Http\Requests\Mobile\v2\Merchant\DefaultSettingRequest;
use App\Http\Resources\Mobile\Merchant\MerchantResource;
use App\Repositories\Mobile\Api\v2\Merchant\MerchantRepository;
use App\Models\Merchant;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Validator;
class MerchantController extends Controller
{
    protected $merchantRepository;
    public function __construct(MerchantRepository $merchantRepository)
    {
        $this->merchantRepository = $merchantRepository;
    }

    public function register(CreateMerchantRequest $request)
    {
        $data = $this->merchantRepository->create($request->all());
        // return response()->json(['status' => 1, 'message' => 'Successfully Registered!'], Response::HTTP_OK);
        if ($data) {
            return response()->json(['status' => 1, 'data' => $data], Response::HTTP_OK);
        } else {
            return response()->json(['status' => 5, 'message' => 'Something Went Wrong!'], Response::HTTP_OK);
        }
    }

    public function set_default(Merchant $merchant, DefaultSettingRequest $request)
    {
        $response = $this->merchantRepository->set_default($merchant, $request->all());
        if ($response) {
            // return response()->json(['status' => 1, 'message' => 'Successfully Set Default!'], Response::HTTP_OK);
            return $response;
        } else {
            return response()->json(['status' => 5, 'message' => 'Something Went Wrong!'], Response::HTTP_OK);
        }
    }
}
