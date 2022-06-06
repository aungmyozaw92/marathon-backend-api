<?php

namespace App\Http\Controllers\Web\Api\v1\SuperMerchant;

use App\Models\Journal;
use App\Models\Merchant;
use Illuminate\Http\Response;
use App\Models\ContactAssociate;
use App\Http\Controllers\Controller;

use App\Http\Resources\SuperMerchant\Merchant\MerchantResource;
use App\Http\Resources\SuperMerchant\Merchant\MerchantCollection;
use App\Repositories\Web\Api\v1\SuperMerchant\MerchantRepository;
use App\Http\Requests\SuperMerchant\Merchant\CreateMerchantRequest;
use App\Http\Requests\SuperMerchant\Merchant\UpdateMerchantRequest;

class MerchantController extends Controller
{
    protected $merchantRepository;

    public function __construct(MerchantRepository $merchantRepository)
    {
        $this->middleware('can:view,merchant')->only('show');
        $this->middleware('can:update,merchant')->only('update');
        $this->middleware('can:delete,merchant')->only('destroy');
        $this->merchantRepository = $merchantRepository;
    }

    public function index()
    {
        $merchants = Merchant::with(
            'merchant_associates',
            'merchant_associates.phones',
            'merchant_associates.emails',
            'merchant_associates.city',
            'merchant_associates.zone',
            'account_informations',
            'city',
            'staff',
            'account'
        )->where('super_merchant_id', auth()->user()->id)->orderBy('id','DECS')->paginate(25);

        return new MerchantCollection($merchants);
    }

    public function store(CreateMerchantRequest $request)
    {
        $merchant = $this->merchantRepository->create($request->all());

        return new MerchantResource($merchant->load([
            'merchant_associates', 'merchant_associates.phones', 'merchant_associates.emails',
            'merchant_associates.city', 'merchant_associates.zone', 'city', 'staff', 'account_informations'
        ]));
    }

    public function show(Merchant $merchant)
    {
        return new MerchantResource($merchant->load([
            'merchant_associates', 'merchant_associates.phones', 'merchant_associates.emails',
            'merchant_associates.city', 'merchant_associates.zone', 'merchant_discounts', 'city', 'staff'
            ,'merchant_associates.account_informations', 'account_informations'
        ]));
    }

    public function update(UpdateMerchantRequest $request, Merchant $merchant)
    {
        $merchant = $this->merchantRepository->update($merchant, $request->all());

        return new MerchantResource($merchant->load([
            'merchant_associates', 'merchant_associates.phones', 'merchant_associates.emails',
            'merchant_associates.city', 'merchant_associates.zone', 'city', 'staff', 'account_informations'
        ]));
    }

    public function destroy(Merchant $merchant)
    {
        $this->merchantRepository->destroy($merchant);
        return response()->json(['status' => 1], Response::HTTP_OK);
        
    }
}
