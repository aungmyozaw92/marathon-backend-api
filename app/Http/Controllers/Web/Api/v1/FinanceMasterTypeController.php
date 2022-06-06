<?php

namespace App\Http\Controllers\Web\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\FinanceMasterType;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\FinanceMasterTypeRepository;
use App\Http\Resources\FinanceMasterType\FinanceMasterTypeResource;
use App\Http\Resources\FinanceMasterType\FinanceMasterTypeCollection;
use App\Http\Requests\FinanceMasterType\CreateFinanceMasterTypeRequest;
use App\Http\Requests\FinanceMasterType\UpdateFinanceMasterTypeRequest;

class FinanceMasterTypeController extends Controller
{
    /**
     * @var FinanceMasterTypeRepository
     */
    protected $financeMasterTypeRepository;

    /**
     * FinanceMasterTypeController constructor.
     *
     * @param FinanceMasterTypeRepository $financeMasterTypeRepository
     */
    public function __construct(FinanceMasterTypeRepository $financeMasterTypeRepository)
    {
        $this->financeMasterTypeRepository = $financeMasterTypeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $accountTypes = $this->financeMasterTypeRepository->all();
        $accountTypes = FinanceMasterType::all();

        return new FinanceMasterTypeCollection($accountTypes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFinanceMasterTypeRequest $request)
    {
        $accountType = $this->financeMasterTypeRepository->create($request->all());

        return new FinanceMasterTypeResource($accountType);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FinanceMasterType  $accountType
     * @return \Illuminate\Http\Response
     */
    public function show(FinanceMasterType $financeMasterType)
    {
        return new FinanceMasterTypeResource($financeMasterType);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FinanceMasterType  $accountType
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFinanceMasterTypeRequest $request, FinanceMasterType $financeMasterType)
    {
        $accountType = $this->financeMasterTypeRepository->update($financeMasterType, $request->all());

        return new FinanceMasterTypeResource($accountType);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FinanceMasterType  $accountType
     * @return \Illuminate\Http\Response
     */
    public function destroy(FinanceMasterType $financeMasterType)
    {
        $this->financeMasterTypeRepository->destroy($financeMasterType);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
