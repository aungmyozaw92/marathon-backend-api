<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Message;
use App\Models\Voucher;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\VoucherMessageRepository;
use App\Http\Resources\VoucherMessage\VoucherMessageResource;
use App\Http\Resources\VoucherMessage\VoucherMessageCollection;
use App\Http\Requests\VoucherMessage\CreateVoucherMessageRequest;
use App\Http\Requests\VoucherMessage\UpdateVoucherMessageRequest;

class VoucherMessageController extends Controller
{
    /**
     * @var VoucherMessageRepository
     */
    protected $voucherMessageRepository;

    /**
     * VoucherMessageController constructor.
     *
     * @param VoucherMessageRepository $voucherMessageRepository
     */
    public function __construct(VoucherMessageRepository $voucherMessageRepository)
    {
        $this->voucherMessageRepository = $voucherMessageRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $voucherMessages = Message::all();

    //     return new VoucherMessageCollection($voucherMessages);
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateVoucherMessageRequest $request)
    {
        $voucherMessage = $this->voucherMessageRepository->create($request->all());

        return new VoucherMessageResource($voucherMessage);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VoucherMessage  $voucherMessage
     * @return \Illuminate\Http\Response
     */
    public function show(Voucher $voucher)
    {

        return new VoucherMessageCollection($voucher->messages()->get());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\VoucherMessage  $voucherMessage
     * @return \Illuminate\Http\Response
     */
    // public function update(UpdateVoucherMessageRequest $request, VoucherMessage $voucherMessage)
    // {
    //     $voucherMessage = $this->voucherMessageRepository->update($voucherMessage, $request->all());

    //     return new VoucherMessageResource($voucherMessage->load(['staff']));
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VoucherMessage  $voucherMessage
     * @return \Illuminate\Http\Response
     */
    // public function destroy(VoucherMessage $voucherMessage)
    // {
    //     $this->voucherMessageRepository->destroy($voucherMessage);

    //     return response()->json(['status' => 1], Response::HTTP_OK);
    // }
}
