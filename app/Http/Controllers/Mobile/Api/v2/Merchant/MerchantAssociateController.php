<?php

namespace App\Http\Controllers\Mobile\Api\v2\Merchant;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MerchantAssociate;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Merchant\MerchantResource;
use App\Http\Resources\Mobile\v2\Merchant\Branch\BranchCollection;
use App\Repositories\Mobile\Api\v2\Merchant\MerchantAssociateRepository;
use App\Http\Requests\Mobile\v2\MerchantAssociate\CreateMerchantAssociateRequest;
use App\Http\Requests\Mobile\v2\MerchantAssociate\UpdateMerchantAssociateRequest;

class MerchantAssociateController extends Controller
{
    protected $merchantAssociateRepository;

    public function __construct(MerchantAssociateRepository $merchantAssociateRepository)
    {
        $this->middleware('can:view,merchant_associate')->only('show');
        $this->middleware('can:update,merchant_associate')->only('update');
        $this->middleware('can:delete,merchant_associate')->only('destroy');
        $this->merchantAssociateRepository = $merchantAssociateRepository;
    }

    public function index()
    {
        $merchant_associates =  MerchantAssociate::where('merchant_id', auth()->user()->id)
                                                    ->get();

        return new BranchCollection($merchant_associates);
    }

    public function store(CreateMerchantAssociateRequest $request)
    {
        $merchantAssociate = $this->merchantAssociateRepository->create($request->all());
        if ($merchantAssociate) {
            return $merchantAssociate;
        } else {
            return response()->json(['status' => 5, 'message' => 'Something Went Wrong!'], Response::HTTP_OK);
        }
    }

    public function update(UpdateMerchantAssociateRequest $request, MerchantAssociate $merchantAssociate)
    {
        $merchantAssociate = $this->merchantAssociateRepository->update($merchantAssociate, $request->all());
        if ($merchantAssociate) {
            return $merchantAssociate;
        } else {
            return response()->json(['status' => 5, 'message' => 'Something Went Wrong!'], Response::HTTP_OK);
        }
    }

    public function destroy(MerchantAssociate $merchantAssociate)
    {
		if($merchantAssociate->merchant->merchant_associates()->count() ===1) {
			return response()->json(['status' => 2, 'message' => 'Please provide at lease one default branch!'], Response::HTTP_OK);
		}
        if ($merchantAssociate->is_default) {
            return response()->json(['status' => 2, 'message' => 'Cannot delete default branch!'], Response::HTTP_OK);
        }
        $this->merchantAssociateRepository->destroy($merchantAssociate);
        return response()->json(['status' => 1, 'message' => 'Successfully Deleted!'], Response::HTTP_OK);
    }
}
