<?php

namespace App\Http\Controllers\Mobile\Api\v2\Merchant;

use App\Models\Merchant;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Mobile\Api\v1\AttachmentRepository;
use App\Http\Resources\Mobile\Attachment\AttachmentCollection;
use App\Http\Resources\Mobile\v2\Merchant\Attachment\AttachmentResource;

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
}
