<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Attachment\AttachmentResource;
use App\Repositories\Web\Api\v1\AttachmentRepository;
use App\Http\Resources\Attachment\AttachmentCollection;

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
        $attachments =  Attachment::all();

        return new AttachmentCollection($attachments);
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
