<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MerchantAssociate;
use App\Http\Controllers\Controller;
use App\Http\Resources\Merchant\MerchantResource;
use App\Repositories\Web\Api\v1\MerchantAssociateRepository;
use App\Http\Resources\MerchantAssociate\MerchantAssociateCollection;
use App\Http\Resources\MerchantAssociate\MerchantAssociateResource;
use App\Http\Requests\MerchantAssociate\CreateMerchantAssociateRequest;
use App\Http\Requests\MerchantAssociate\UpdateMerchantAssociateRequest;

class MerchantAssociateController extends Controller
{
    protected $merchantAssociateRepository;

    public function __construct(MerchantAssociateRepository $merchantAssociateRepository)
    {
        // $this->middleware('can:view,merchantAssociate')->only('show');
        $this->middleware('can:update,merchant_associate')->only('update');
        $this->middleware('can:delete,merchant_associate')->only('destroy');
        $this->merchantAssociateRepository = $merchantAssociateRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $merchant_associates = $this->merchantAssociateRepository->all();
        $merchant_associates = MerchantAssociate::where('merchant_id', auth()->user()->id)->get();

        return new MerchantAssociateCollection($merchant_associates->load([
            'phones', 'emails', 'city', 'zone', 'account_informations'
        ]));
    }

    public function store(CreateMerchantAssociateRequest $request)
    {
        $merchant = $this->merchantAssociateRepository->create($request->all());

        return new MerchantAssociateResource($merchant->merchant_associates()->latest()->first()->load([
            'phones', 'emails', 'city', 'zone', 'account_informations'
        ]));
    }

    public function update(UpdateMerchantAssociateRequest $request, MerchantAssociate $merchantAssociate)
    {
        $merchant = $this->merchantAssociateRepository->update($merchantAssociate, $request->all());

        return new MerchantAssociateResource($merchant->merchant_associates()->latest('updated_at')->first()->load([
            'phones', 'emails', 'city', 'zone', 'account_informations'
        ]));
    }

    public function destroy(MerchantAssociate $merchantAssociate)
    {
        $this->merchantAssociateRepository->destroy($merchantAssociate);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
