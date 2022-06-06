<?php

namespace App\Http\Controllers\Web\Api\v1;

use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\FinanceTableOfAuthority;
use App\Http\Resources\FinanceTableOfAuthority\FinanceTableOfAuthorityCollection;
use App\Http\Requests\FinanceTableOfAuthority\CreateFinanceTableOfAuthorityRequest;
use App\Http\Requests\FinanceTableOfAuthority\UpdateFinanceTableOfAuthorityRequest;
use App\Repositories\Web\Api\v1\FinanceTableOfAuthorityRepository;
use App\Http\Resources\FinanceTableOfAuthority\FinanceTableOfAuthorityResource;

class FinanceTableOfAuthorityController extends Controller
{
    /**
     * @var FinanceTableOfAuthorityRepository
     */
    protected $finance_toa_Repository;

    /**
     * FinanceTableOfAuthorityController constructor.
     *
     * @param FinanceTableOfAuthorityRepository $finance_toa_Repository
     */
    public function __construct(FinanceTableOfAuthorityRepository $finance_toa_Repository)
    {
        $this->finance_toa_Repository = $finance_toa_Repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $finance_toa = $this->finance_toa_Repository->all();
        $finance_toa = FinanceTableOfAuthority::all();

        return new FinanceTableOfAuthorityCollection($finance_toa->load(['manager','staff']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFinanceTableOfAuthorityRequest $request)
    {
        $finance_toa = $this->finance_toa_Repository->create($request->all());

        return new FinanceTableOfAuthorityResource($finance_toa->load(['manager','staff']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FinanceTableOfAuthority  $finance_toa
     * @return \Illuminate\Http\Response
     */
    public function show(FinanceTableOfAuthority $finance_table_of_authority)
    {
        return new FinanceTableOfAuthorityResource($finance_table_of_authority->load(['manager','staff']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FinanceTableOfAuthority  $finance_toa
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFinanceTableOfAuthorityRequest $request, FinanceTableOfAuthority $finance_toa)
    {
        $finance_toa = $this->finance_toa_Repository->update($finance_toa, $request->all());

        return new FinanceTableOfAuthorityResource($finance_toa->load(['manager','staff']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FinanceTableOfAuthority  $finance_toa
     * @return \Illuminate\Http\Response
     */
    public function destroy(FinanceTableOfAuthority $finance_toa)
    {
        $this->finance_toa_Repository->destroy($finance_toa);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
