<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Agent;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Hash;

class AgentRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Agent::class;
    }

    /**
     * @param array $data
     *
     * @return Agent
     */
    public function create(array $data) : Agent
    {
        $agent = Agent::create([
            'name'              => $data['name'],
            'account_code'      => isset($data['account_code']) ? $data['account_code'] : null,
            'username'          => $data['username'],
            'password'          => Hash::make($data['password']),
            'city_id'           => $data['city_id'],
            'agent_badge_id'    => $data['agent_badge_id'],
            'is_active'         => isset($data['is_active'])?$data['is_active']:1,
            'on_demand'         => isset($data['on_demand'])?$data['on_demand']:0,
            'agent_branch'      => isset($data['agent_branch'])?$data['agent_branch']:0,
            'delivery_commission' => isset($data['delivery_commission'])?$data['delivery_commission']:0,
            'phone' => isset($data['phone'])?$data['phone']:null,
            'address' => isset($data['address'])?$data['address']:null,
            'shop_name' => isset($data['shop_name']) ? $data['shop_name'] : null,
            'created_by'        => auth()->user()->id,
        ]);

        $accountRepository = new AccountRepository();
        $account = [
            'city_id' => $agent->city_id,
            'accountable_type' => 'Agent',
            'accountable_id' => $agent->id,
        ];
        $accountRepository->create($account);

        return $agent;
    }

    /**
     * @param Agent  $agent
     * @param array $data
     *
     * @return mixed
     */
    public function update(Agent $agent, array $data) : Agent
    {
        $agent->name = isset($data['name']) ? $data['name'] : $agent->name ;
        $agent->account_code = isset($data['account_code']) ? $data['account_code'] : $agent->account_code ;
        $agent->city_id = isset($data['city_id']) ? $data['city_id']: $agent->city_id;
        $agent->username = isset($data['username']) ? $data['username'] : $agent->username;
        // $agent->password = $data['password'];'password'         => isset($data['password'])?$data['password']:0,
        $agent->is_active = isset($data['is_active'])?$data['is_active']:$agent->is_active;
        $agent->agent_badge_id = isset($data['agent_badge_id']) ? $data['agent_badge_id'] : $agent->agent_badge_id;
        $agent->on_demand = isset($data['on_demand'])?$data['on_demand']:$agent->on_demand;
        $agent->agent_branch = isset($data['agent_branch'])?$data['agent_branch']:$agent->agent_branch;
        $agent->delivery_commission = isset($data['delivery_commission'])?$data['delivery_commission']:$agent->delivery_commission;
        $agent->password = isset($data['password'])?Hash::make($data['password']):$agent->password;
        $agent->phone = isset($data['phone'])?$data['phone'] : null;
        $agent->address = isset($data['address'])?$data['address'] : null;
        $agent->shop_name = isset($data['shop_name'])?$data['shop_name'] : null;


        if ($agent->isDirty()) {
            $agent->updated_by = auth()->user()->id;
            $agent->save();
        }

        if (!$agent->account) {
            $accountRepository = new AccountRepository();
            $account = [
                'city_id' => $agent->city_id,
                'accountable_type' => 'Agent',
                'accountable_id' => $agent->id,
            ];
            $accountRepository->create($account);
        }

        return $agent->refresh();
    }

    /**
     * @param Agent $agent
     */
    public function destroy(Agent $agent)
    {
        $deleted = $this->deleteById($agent->id);

        if ($deleted) {
            $agent->deleted_by = auth()->user()->id;
            $agent->save();
        }
    }
}
