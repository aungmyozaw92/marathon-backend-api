<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Models\Merchant;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Mobile\Api\v1\AttachmentRepository;
use App\Http\Resources\Mobile\Attachment\AttachmentResource;
use App\Http\Resources\Mobile\Attachment\AttachmentCollection;

class AttachmentController extends Controller
{
    /**
     * @var AttachmentRepository
     */
    protected $attachmentRepository;

    /**
     * AttachmentController constructor.
     *
     * @param AttachmentRepository $attachmentRepository
     */
    public function __construct(AttachmentRepository $attachmentRepository)
    {
        $this->attachmentRepository = $attachmentRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attachments = Attachment::all();

        return new AttachmentCollection($attachments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (request()->has(['file'])) {
            $merchant = Merchant::find(auth()->user()->id);
            $attachment = $this->attachmentRepository->upload_profile($merchant, request()->only(['file']));
            return new AttachmentResource($attachment);
        } else {
            return response()->json(['status' => 2, 'message' => "Please select file!"], Response::HTTP_OK);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Attachment  $attachment
     * @return \Illuminate\Http\Response
     */
    public function show(Attachment $attachment)
    {
        return new AttachmentResource($attachment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Attachment  $attachment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attachment $attachment)
    {
        $this->attachmentRepository->destroy($attachment);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
