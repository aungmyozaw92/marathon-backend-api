<?php

namespace App\Http\Controllers\Mobile\Api\v2\Merchant;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\AccountInformation;
use App\Http\Controllers\Controller;
use App\Repositories\Mobile\Api\v2\Merchant\AccountInformationRepository;
use App\Http\Requests\Mobile\v2\AccountInformation\CreateAccountInformationRequest;
use App\Http\Requests\Mobile\v2\AccountInformation\UpdateAccountInformationRequest;
use App\Http\Resources\Mobile\v2\Merchant\AccountInformation\AccountInformationCollection;

class AccountInformationController extends Controller
{
    //
    protected $accountInformationRepository;

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
        $accountInformations =  AccountInformation::where('resourceable_type', 'Merchant')
                                                    ->where('resourceable_id', auth()->user()->id)
                                                    ->get();

        return new AccountInformationCollection($accountInformations->load(['bank']));
    }

    public function store(CreateAccountInformationRequest $request)
    {
        $account = $this->accountInformationRepository->create($request->all());
        if ($account) {
            return response()->json(['status' => 1, 'message' => 'Successfully  Created!'], Response::HTTP_OK);
        } else {
            return response()->json(['status' => 5, 'message' => 'Something Went Wrong!'], Response::HTTP_OK);
        }
    }
    public function update(UpdateAccountInformationRequest $request, AccountInformation $accountInformation)
    {
        $account = $this->accountInformationRepository->update($accountInformation, $request->all());
        if ($account) {
            return response()->json(['status' => 1, 'message' => 'Successfully Updated!'], Response::HTTP_OK);
        } else {
            return response()->json(['status' => 5, 'message' => 'Something Went Wrong!'], Response::HTTP_OK);
        }
    }
    public function destroy(AccountInformation $accountInformation)
    {
		if ($accountInformation->is_default) {
			return response()->json(['status' => 2, 'message' => 'Cannot delete default branch!'], Response::HTTP_OK);
		}
        $this->accountInformationRepository->destroy($accountInformation);

        return response()->json(['status' => 1, 'message' => 'Successfully Deleted!'], Response::HTTP_OK);
    }
}
