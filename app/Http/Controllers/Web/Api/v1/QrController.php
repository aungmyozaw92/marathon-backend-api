<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Qr;
use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Qr\QrResource;
use App\Http\Resources\Qr\QrCollection;
use App\Http\Requests\Qr\CreateQrRequest;
use App\Repositories\Web\Api\v1\QrRepository;
use App\Repositories\Web\Api\v1\QrAssociateRepository;

class QrController extends Controller
{
    /**
     * @var QrRepository
     */
    protected $qrRepository;

    /**
     * QrController constructor.
     *
     * @param QrRepository $qrRepository
     */
    public function __construct(QrRepository $qrRepository, QrAssociateRepository $qr_associateRepository)
    {
        $this->qrRepository = $qrRepository;
        $this->qr_associateRepository = $qr_associateRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $qrs =  Qr::all();

        return new QrCollection($qrs);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateQrRequest $request)
    {
        $actor_type = $request->get('actor_type');
        if ($actor_type == 'Merchant') {
            $actor_id = $request->get('merchant_id');
        }elseif ($actor_type == 'Customer') {
            $actor_id = $request->get('customer_id');
        }else{
            $actor_id = $request->get('agent_id');
        }
        $qr = Qr::where('actor_id',$actor_id)->where('actor_type',$actor_type)->first();
        if ($qr) {
            $qr_associate = $this->qr_associateRepository->create($qr);
        }else{
            $qr = $this->qrRepository->create($request->all());
        }

        return new QrResource($qr);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Qr  $qr
     * @return \Illuminate\Http\Response
     */
    public function show(Qr $qr)
    {
        return new QrResource($qr->load(['qr_associates']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Qr  $qr
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateQrRequest $request, Qr $qr)
    {
        $qr = $this->qrRepository->update($qr, $request->all());

        return new QrResource($qr->load(['qr_associates']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Qr  $qr
     * @return \Illuminate\Http\Response
     */
    public function destroy(Qr $qr)
    {
        $this->qrRepository->destroy($qr);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
