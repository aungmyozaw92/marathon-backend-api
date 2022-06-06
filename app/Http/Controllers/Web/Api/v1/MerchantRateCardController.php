<?php

namespace App\Http\Controllers\Web\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Imports\RateCardImport;
use App\Models\MerchantRateCard;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\Agent\AgentCollection;
use App\Repositories\Web\Api\v1\MerchantRateCardRepository;
use App\Http\Resources\MerchantRateCard\MerchantRateCardResource;
use App\Http\Resources\MerchantRateCard\MerchantRateCardCollection;
use App\Http\Requests\MerchantRateCard\CreateMerchantRateCardRequest;
use App\Http\Requests\MerchantRateCard\UpdateMerchantRateCardRequest;

class MerchantRateCardController extends Controller
{
    /**
     * @var MerchantRateCardRepository
     */
    protected $merchantRateCardRepository;

    /**
     * MerchantRateCardController constructor.
     *
     * @param MerchantRateCardRepository $merchantRateCardRepository
     */
    public function __construct(MerchantRateCardRepository $merchantRateCardRepository)
    {
        $this->merchantRateCardRepository = $merchantRateCardRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $merchant_rate_cards = $this->merchantRateCardRepository->all();
        $merchant_rate_cards = MerchantRateCard::with([
                                'merchant', 'merchant_associate', 'sender_city','receiver_city',
                                'sender_zone','receiver_zone','discount_type'
                            ])->filter(request()->only([
                                    'search', 'merchant_id','merchant_associate_id','sortBy','orderBy'
                            ]));
                            
        if (request()->has('paginate')) {
            $merchant_rate_cards = $merchant_rate_cards->paginate(request()->get('paginate'));
        } else {
            $merchant_rate_cards = $merchant_rate_cards->get();
        }

        return new MerchantRateCardCollection($merchant_rate_cards);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateMerchantRateCardRequest $request)
    {
        $merchantRateCard = $this->merchantRateCardRepository->create($request->all());

        return new MerchantRateCardResource($merchantRateCard);
    }
 
    /**
     * Display the specified resource.
     *
     * @param  \App\MerchantRateCard  $merchantRateCard
     * @return \Illuminate\Http\Response
     */
    public function show(MerchantRateCard $merchantRateCard)
    {
        return new MerchantRateCardResource($merchantRateCard->load(['zones','agent','branch']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MerchantRateCard  $merchantRateCard
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMerchantRateCardRequest $request, MerchantRateCard $merchantRateCard)
    {
        $merchantRateCard = $this->merchantRateCardRepository->update($merchantRateCard, $request->all());

        return new MerchantRateCardResource($merchantRateCard);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MerchantRateCard  $merchantRateCard
     * @return \Illuminate\Http\Response
     */
    public function destroy(MerchantRateCard $merchantRateCard)
    {
        $this->merchantRateCardRepository->destroy($merchantRateCard);

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        Excel::import(new RateCardImport, request()->file('file'));
         return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
