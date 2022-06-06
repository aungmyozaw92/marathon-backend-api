<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\AccountInformation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccountInformation\AccountInformationResource;
use App\Http\Resources\AccountInformation\AccountInformationCollection;
use App\Http\Requests\AccountInformation\CreateAccountInformationRequest;
use App\Http\Requests\AccountInformation\UpdateAccountInformationRequest;
use App\Repositories\Web\Api\v1\AccountInformationRepository;

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
        $this->accountInformationRepository = $accountInformationRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accountInformations =  AccountInformation::with('bank')->all();

        return new AccountInformationCollection($accountInformations);
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
