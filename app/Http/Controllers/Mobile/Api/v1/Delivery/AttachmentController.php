<?php

namespace App\Http\Controllers\Mobile\Api\v1\Delivery;

use Illuminate\Http\Response;


use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\Upload\AttachmentUploadRequest;
use App\Repositories\Mobile\Api\v1\Delivery\AttachmentRepository;
use App\Http\Resources\Mobile\Delivery\Attachment\AttachmentResource;
use App\Models\Attachment;

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

    public function upload(AttachmentUploadRequest $request)
    {
        $attachment = $this->attachmentRepository->create($request->all());

        return new AttachmentResource($attachment);
    }

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
