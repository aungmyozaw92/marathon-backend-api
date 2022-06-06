<?php

namespace App\Http\Controllers\Mobile\Api\v2\Merchant;

use App\Models\Tag;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Tag\TagResource;
use App\Http\Resources\Mobile\Tag\TagCollection;
use App\Repositories\Mobile\Api\v1\TagRepository;
use App\Http\Requests\Mobile\Tag\CreateTagRequest;
use App\Http\Requests\Mobile\Tag\UpdateTagRequest;

class TagController extends Controller
{
    /**
     * @var TagRepository
     */
    protected $tagRepository;

    /**
     * TagController constructor.
     *
     * @param TagRepository $tagRepository
     */
    public function __construct(TagRepository $tagRepository)
    {
        $this->middleware('can:view,tag')->only('show');
        $this->middleware('can:update,tag')->only('update');
        $this->middleware('can:delete,tag')->only('destroy');
        $this->tagRepository = $tagRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTagRequest $request)
    {
        $this->tagRepository->create($request->all());
        return response()->json(['status' => 1, 'message' => 'Successfully Created!'], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Agent  $tag
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $this->tagRepository->update($tag, $request->all());
        return response()->json(['status' => 1, 'message' => 'Successfully Updated!'], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag)
    {
        $this->tagRepository->destroy($tag);
        return response()->json(['status' => 1, 'message' => 'Successfully Deleted!'], Response::HTTP_OK);
    }
}
