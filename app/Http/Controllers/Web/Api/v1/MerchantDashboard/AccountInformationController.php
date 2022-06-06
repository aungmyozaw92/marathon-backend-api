<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Models\AccountInformation;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\MerchantDashboard\AccountInformation\AccountInformationResource;
use App\Http\Resources\MerchantDashboard\AccountInformation\AccountInformationCollection;
use App\Http\Requests\MerchantDashboard\AccountInformation\CreateAccountInformationRequest;
use App\Http\Requests\MerchantDashboard\AccountInformation\UpdateAccountInformationRequest;
use App\Repositories\Web\Api\v1\MerchantDashboard\AccountInformationRepository;

class AccountInformationController extends Controller
{
    /**
     * @var AccountInformationRepository
     */
    protected $accountInformationRepository;

    /**
     * accountInformationController constructor.
     *
     * @param AccountInformationRepository $accountInformationRepository
     */
    public function __construct(AccountInformationRepository $accountInformationRepository)
    {
        $this->middleware('can:view,account_information')->only('show');
        $this->middleware('can:update,account_information')->only('update');
        $this->middleware('can:delete,account_information')->only('destroy');
        $this->accountInformationRepository = $accountInformationRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $merchant = Merchant::find(auth()->user()->id);
        $accountInformations =  AccountInformation::where('resourceable_type', 'Merchant')
                                                    ->where('resourceable_id', $merchant->id)
                                                    ->get();

        return new AccountInformationCollection($accountInformations->load(['bank']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateAccountInformationRequest $request)
    {
        $accountInformation = $this->accountInformationRepository->create($request->all());

        return new AccountInformationResource($accountInformation->load(['bank']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AccountInformation  $accountInformation
     * @return \Illuminate\Http\Response
     */
    public function show(AccountInformation $accountInformation)
    {
        return new AccountInformationResource($accountInformation->load(['bank']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AccountInformation  $accountInformation
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAccountInformationRequest $request, AccountInformation $accountInformation)
    {
        $accountInformation = $this->accountInformationRepository->update($accountInformation, $request->all());

        return new AccountInformationResource($accountInformation->load(['bank']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AccountInformation  $accountInformation
     * @return \Illuminate\Http\Response
     */
    public function destroy(AccountInformation $accountInformation)
    {
        $this->accountInformationRepository->destroy($accountInformation);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
