<?php

namespace App\Http\Controllers\Mobile\Api\v1\Agent;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\VoucherMessage\VoucherMessageResource;
use App\Http\Resources\VoucherMessage\VoucherMessageCollection;
use App\Models\Staff;
use App\Models\Voucher;

class VoucherMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Voucher $voucher)
    {
        // dd(method_exists(auth()->user(), 'role'));
        $agent_team = Staff::whereIn('role_id', [3, 7])->pluck('id');
        $team_messages = $voucher->messages()->whereIn('messenger_id', $agent_team)->get();
        // return $team_messages;
        $agent_messages = $voucher->messages()->where('messenger_id', auth()->user()->id)->get();
        // return count($agent_messages);
        $return_messages = $agent_messages->merge($team_messages);
        return new VoucherMessageCollection($return_messages);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
