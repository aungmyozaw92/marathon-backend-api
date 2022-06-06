<?php

namespace App\Http\Controllers\Mobile\Api\v1\Agent;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Mobile\Api\v1\Agent\AttachmentRepository;
use App\Http\Resources\Mobile\Agent\Attachment\AttachmentResource;
use App\Http\Resources\Mobile\Agent\Attachment\AttachmentCollection;

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
            $attachment = $this->attachmentRepository->create_attachmet(auth()->user(), request()->only(['file']));
            return new AttachmentResource($attachment);
        }else{
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
