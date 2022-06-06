<?php

namespace App\Http\Controllers\Web\Api\v1\SuperMerchant;

use App\Models\Journal;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\ContactAssociate;

use App\Models\MerchantAssociate;
use App\Http\Controllers\Controller;
use App\Http\Resources\SuperMerchant\Merchant\MerchantResource;
use App\Repositories\Web\Api\v1\SuperMerchant\MerchantAssociateRepository;
use App\Http\Requests\SuperMerchant\MerchantAssociate\CreateMerchantAssociateRequest;
use App\Http\Requests\SuperMerchant\MerchantAssociate\UpdateMerchantAssociateRequest;

class MerchantAssociateController extends Controller
{
    protected $merchantAssociateRepository;

    public function __construct(MerchantAssociateRepository $merchantAssociateRepository)
    {
        $this->middleware('can:create,merchant')->only('store');
        $this->middleware('can:update,merchant')->only('update');
        $this->middleware('can:delete,merchant')->only('destroy');
        $this->merchantAssociateRepository = $merchantAssociateRepository;
    }

    public function store(Merchant $merchant, CreateMerchantAssociateRequest $request)
    {
        $merchant = $this->merchantAssociateRepository->create_merchant_associate($merchant, $request->all());

        return new MerchantResource($merchant->load([
            'merchant_associates', 'merchant_associates.phones', 'merchant_associates.emails',
            'merchant_associates.city', 'merchant_associates.zone', 'city', 'staff', 'account_informations'
        ]));
    }

    public function update(Request $request, Merchant $merchant, MerchantAssociate $merchant_associate)
    {
        if ($merchant->id != $merchant_associate->merchant_id) {
            return response()->json([
                'status' => 2,
                'message' => 'merchant and merchant associate do not match !'
            ], Response::HTTP_OK);
        }
        $merchant = $this->merchantAssociateRepository->update_merchant_associate($merchant, $merchant_associate, $request->all());

        return new MerchantResource($merchant->load([
            'merchant_associates', 'merchant_associates.phones', 'merchant_associates.emails',
            'merchant_associates.city', 'merchant_associates.zone', 'city', 'staff', 'account_informations'
        ]));
    }

    public function destroy(Merchant $merchant, MerchantAssociate $merchant_associate)
    {
        if ($merchant->id != $merchant_associate->merchant_id) {
            return response()->json([
                'status' => 2,
                'message' => 'merchant and merchant associate do not match !'
            ], Response::HTTP_OK);
        }
        $this->merchantAssociateRepository->destroy($merchant_associate);
        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
