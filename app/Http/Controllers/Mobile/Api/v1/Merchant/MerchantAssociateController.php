<?php

namespace App\Http\Controllers\Mobile\Api\v1\Merchant;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MerchantAssociate;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Merchant\MerchantResource;
use App\Repositories\Mobile\Api\v1\MerchantAssociateRepository;
use App\Http\Requests\Mobile\MerchantAssociate\CreateMerchantAssociateRequest;
use App\Http\Requests\Mobile\MerchantAssociate\UpdateMerchantAssociateRequest;

class MerchantAssociateController extends Controller
{
    protected $merchantAssociateRepository;

    public function __construct(MerchantAssociateRepository $merchantAssociateRepository)
    {
        $this->merchantAssociateRepository = $merchantAssociateRepository;
    }

    public function store(CreateMerchantAssociateRequest $request)
    {
        $merchant = $this->merchantAssociateRepository->create($request->all());

        return new MerchantResource($merchant->load([
            'merchant_associates', 'merchant_associates.phones', 'merchant_associates.emails',
            'merchant_associates.city', 'merchant_associates.zone'
        ]));
    }

    public function update(UpdateMerchantAssociateRequest $request, MerchantAssociate $merchantAssociate)
    {
        $merchant = $this->merchantAssociateRepository->update($merchantAssociate, $request->all());

        return new MerchantResource($merchant->load([
            'merchant_associates', 'merchant_associates.phones', 'merchant_associates.emails',
            'merchant_associates.city', 'merchant_associates.zone'
        ]));
    }

    public function destroy(MerchantAssociate $merchantAssociate)
    {
        $this->merchantAssociateRepository->destroy($merchantAssociate);
        return response()->json(['status' => 1, 'message' => 'Successfully Deleted!'], Response::HTTP_OK);
    }
}
