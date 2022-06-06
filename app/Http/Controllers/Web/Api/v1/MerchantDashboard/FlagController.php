<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;


use App\Models\Flag;
use App\Http\Controllers\Controller;
use App\Http\Resources\Flag\FlagResource;
use App\Http\Resources\Flag\FlagCollection;

class FlagController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $flags = Flag::all();

        return new FlagCollection($flags);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Flag  $flag
     * @return \Illuminate\Http\Response
     */
    public function show(Flag $flag)
    {
        return new FlagResource($flag);
    }

}
