<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Agent;
use App\Models\Branch;
use App\Models\Journal;
use App\Exports\AgentData;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\Agent\AgentResource;
use App\Exports\AgentTransactionHistorySheet;
use App\Http\Resources\Agent\AgentCollection;
use App\Http\Requests\Agent\CreateAgentRequest;
use App\Http\Requests\Agent\UpdateAgentRequest;
use App\Repositories\Web\Api\v1\AgentRepository;
use App\Http\Resources\TransactionJournal\TransactionJournalCollection;

class AgentController extends Controller
{
    /**
     * @var AgentRepository
     */
    protected $agentRepository;

    /**
     * AgentController constructor.
     *
     * @param AgentRepository $agentRepository
     */
    public function __construct(AgentRepository $agentRepository)
    {
        $this->agentRepository = $agentRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!request()->exists('balance')) {
            request()->request->add(['balance' => '']);
        }

        if (request()->has('export')) {
            $filename = 'agents.xlsx';
            Excel::store(new AgentData, $filename, 'public', null, [
                    'visibility' => 'public',
            ]);
            $file = storage_path('/app/public/agents.xlsx');
            return response()->download($file)->deleteFileAfterSend();
        }
        
        $agents =  Agent::with(['city','account', 'agent_badge'])
                            ->filter(request()->only([
                                'search', 'city_id','username', 'name', 'phone',
                                'is_active', 'balance', 'balance_operator', 'agent_badge_id',
                                'shop_name'
                            ]))->order(request()->only([
                                'sortBy', 'orderBy'
                            ]));

        if (request()->has('paginate')) {
            $agents = $agents->paginate(request()->get('paginate'));
        } else {
            $agents = $agents->get();
        }

        return new AgentCollection($agents);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateAgentRequest $request)
    {
        $checkAgent = Agent::where('city_id', $request->get('city_id'))->where('is_active', true)->exists();
        // if ($checkAgent && $request->get('is_active') == true) {
        //     return response()->json([
        //         'status' => 2,
        //         'message' => "Already active agent!",
        //     ], 200);
        // }
        $agent = $this->agentRepository->create($request->all());

        return new AgentResource($agent->load(['city','account', 'agent_badge']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function show(Agent $agent)
    {
        return new AgentResource($agent->load(['city','account', 'agent_badge']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAgentRequest $request, Agent $agent)
    {
        // $checkAgent = Agent::where('city_id', $request->get('city_id'))->where('is_active', true)->first();
        // if ($checkAgent && $agent->id != $checkAgent->id) {
        //     return response()->json([
        //         'status' => 2,
        //         'message' => "Already active agent!",
        //     ], 200);
        // }
        $agent = $this->agentRepository->update($agent, $request->all());

        return new AgentResource($agent->load(['city','account', 'agent_badge']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function destroy(Agent $agent)
    {
        $agent->is_active = false;
        $agent->save();
        $this->agentRepository->destroy($agent);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }

    public function transaction_lists(Agent $agent)
    {
        $agent_account = $agent->account;
        if (request()->has('export')) {
            $filename = 'agent_transaction.xlsx';
            Excel::store(new AgentTransactionHistorySheet($agent_account->id), $filename, 'public', null, [
                    'visibility' => 'public',
            ]);
            $file = storage_path('/app/public/agent_transaction.xlsx');
            return response()->download($file)->deleteFileAfterSend();
        }
        
        $journals =  Journal::with(['resourceable',
                                'resourceable.payment_type',
                                'resourceable.delivery_status',
                                'resourceable.receiver',
                                'resourceable.receiver_city',
                                'resourceable.sender_city',
                                'resourceable.pickup.sender',
                                'credit_account','debit_account'
                                ])
                            ->getTransactionJournal($agent_account->id, request()->only([
                                'start_date', 'end_date',
                            ]))->paginate(25);

        return new TransactionJournalCollection($journals);
    }
}
