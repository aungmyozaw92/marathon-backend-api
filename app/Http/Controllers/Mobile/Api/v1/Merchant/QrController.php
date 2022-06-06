<?php

namespace App\Http\Controllers\Mobile\Api\v1\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\Qr\CheckQrRequest;
use App\Repositories\Mobile\Api\v1\QrRepository;

class QrController extends Controller
{
    /**
     * @var QrRepository
     */
    protected $qrRepository;

    /**
     * CityController constructor.
     *
     * @param QrRepository $qrRepository
     */
    public function __construct(QrRepository $qrRepository)
    {
        $this->qrRepository = $qrRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function check_qr_code(CheckQrRequest $request)
    {
        $response = $this->qrRepository->checkQrCode($request->get('qr_code'));
        return response()->json(['status' => $response['status'], 'message' => $response['message']]);
    }

}
