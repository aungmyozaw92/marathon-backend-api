<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Tag;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Tag\TagResource;
use App\Http\Resources\Tag\TagCollection;
use App\Http\Requests\Tag\CreateTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Repositories\Web\Api\v1\TagRepository;

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
        $this->tagRepository = $tagRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = Tag::with(['merchant'])->filter(request()->only([
                    'name', 'merchant_id'
                ]));
        if (request()->has('paginate')) {
            $tags = $tags->paginate(request()->get('paginate'));
        } else {
            $tags = $tags->get();
        }

        return new TagCollection($tags);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTagRequest $request)
    {
        $tag = $this->tagRepository->create($request->all());

        return new TagResource($tag->load(['merchant']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Agent  $tag
     * @return \Illuminate\Http\Response
     */
    public function show(Tag $tag)
    {
        return new TagResource($tag->load(['merchant']));
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
        
        $tag = $this->tagRepository->update($tag, $request->all());

        return new TagResource($tag->load(['merchant']));
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
        return response()->json(['status' => 1], Response::HTTP_OK);
    }

    
}
