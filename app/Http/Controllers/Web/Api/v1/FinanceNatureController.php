<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\FinanceNature;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\FinanceNatureRepository;
use App\Http\Resources\FinanceNature\FinanceNatureResource;
use App\Http\Resources\FinanceNature\FinanceNatureCollection;
use App\Http\Requests\FinanceNature\CreateFinanceNatureRequest;
use App\Http\Requests\FinanceNature\UpdateFinanceNatureRequest;

class FinanceNatureController extends Controller
{
    /**
     * @var FinanceNatureRepository
     */
    protected $financeNatureRepository;

    /**
     * FinanceNatureController constructor.
     *
     * @param FinanceNatureRepository $financeNatureRepository
     */
    public function __construct(FinanceNatureRepository $financeNatureRepository)
    {
        $this->financeNatureRepository = $financeNatureRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $accountTypes = $this->financeNatureRepository->all();
        $accountTypes = FinanceNature::all();

        return new FinanceNatureCollection($accountTypes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFinanceNatureRequest $request)
    {
        $accountType = $this->financeNatureRepository->create($request->all());

        return new FinanceNatureResource($accountType);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FinanceNature  $accountType
     * @return \Illuminate\Http\Response
     */
    public function show(FinanceNature $financeNature)
    {
        return new FinanceNatureResource($financeNature);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FinanceNature  $accountType
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFinanceNatureRequest $request, FinanceNature $financeNature)
    {
        $accountType = $this->financeNatureRepository->update($financeNature, $request->all());

        return new FinanceNatureResource($accountType);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FinanceNature  $accountType
     * @return \Illuminate\Http\Response
     */
    public function destroy(FinanceNature $financeNature)
    {
        $this->financeNatureRepository->destroy($financeNature);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
