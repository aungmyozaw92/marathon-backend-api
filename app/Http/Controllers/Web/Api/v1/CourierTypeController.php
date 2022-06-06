<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\CourierType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourierType\CourierTypeResource;
use App\Http\Resources\CourierType\CourierTypeCollection;
use App\Http\Requests\CourierType\CreateCourierTypeRequest;
use App\Http\Requests\CourierType\UpdateCourierTypeRequest;

class CourierTypeController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courierTypes = CourierType::all();

        return new CourierTypeCollection($courierTypes);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCourierTypeRequest $request)
    {
        $courierType = $request->storeCourierType();

        return new CourierTypeResource($courierType);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CourierType  $courierType
     * @return \Illuminate\Http\Response
     */
    public function show(CourierType $courierType)
    {
        return new CourierTypeResource($courierType);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CourierType  $courierType
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCourierTypeRequest $request, CourierType $courierType)
    {
        $courierType = $request->updateCourierType($courierType);

        return new CourierTypeResource($courierType);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CourierType  $courierType
     * @return \Illuminate\Http\Response
     */
    public function destroy(CourierType $courierType)
    {
        $deleted = $courierType->delete();

        if ($deleted) {
            $courierType->deleted_by = auth()->user()->id;
            $courierType->save();
        }

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
