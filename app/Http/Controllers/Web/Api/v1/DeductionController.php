<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Deduction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Deduction\DeductionResource;
use App\Repositories\Web\Api\v1\DeductionRepository;
use App\Http\Resources\Deduction\DeductionCollection;
use App\Http\Requests\Deduction\CreateDeductionRequest;
use App\Http\Requests\Deduction\UpdateDeductionRequest;

class DeductionController extends Controller
{
    /**
     * @var DeductionRepository
     */
    protected $deductionRepository;

    /**
     * BadgeController constructor.
     *
     * @param DeductionRepository $deductionRepository
     */
    public function __construct(DeductionRepository $deductionRepository)
    {
        $this->deductionRepository = $deductionRepository;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deductions = Deduction::all();

        return new DeductionCollection($deductions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateDeductionRequest $request)
    {
        $deduction = $this->deductionRepository->create($request->all());

        return new DeductionResource($deduction);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Deduction  $deductionBadge
     * @return \Illuminate\Http\Response
     */
    public function show(Deduction $deduction)
    {
        return new DeductionResource($deduction);
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\Deduction  $deduction
    * @return \Illuminate\Http\Response
    */
    public function update(UpdateDeductionRequest $request, Deduction $deduction)
    {
        $deduction = $this->deductionRepository->update($deduction, $request->all());

        return new DeductionResource($deduction);
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Deduction  $deduction
    * @return \Illuminate\Http\Response
    */
    public function destroy(Deduction $deduction)
    {
        $this->deductionRepository->destroy($deduction);

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
