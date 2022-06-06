<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\AgentBadge;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Storage;

class AgentBadgeRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return AgentBadge::class;
    }

    /**
     * @param array $data
     *
     * @return AgentBadge
     */
    public function create(array $data) : AgentBadge
    {
        $logo = $data['logo'];

        if (gettype($logo) == 'string') {
            $logo_name = 'logo_' . time() . '.' . 'png';
            $logo_content = base64_decode($logo);
        } else {
            $logo_name = 'logo_' . time() . '_' . $logo->getClientOriginalName();
            $logo_content = file_get_contents($logo);
        }
        Storage::disk('dospace')->put('agent_badge_logo/' . $logo_name, $logo_content);
        Storage::setVisibility('agent_badge_logo/' . $logo_name, "public");

        return AgentBadge::create([
            'name'                => $data['name'],
            'deposit'             => $data['deposit'],
            'logo'                => $logo_name,
            'monthly_reward'      => $data['monthly_reward'],
            'delivery_points'     => $data['delivery_points'],
            'weekly_payment'      => $data['weekly_payment'],
            'monthly_good_credit' => $data['monthly_good_credit']
        ]);
    }

    /**
     * @param AgentBadge  $agentBadge
     * @param array $data
     *
     * @return mixed
     */
    public function update(AgentBadge $agentBadge, array $data) : AgentBadge
    {
        if (request()->has('logo')) {
            $logo = $data['logo'];
            Storage::disk('dospace')->delete('agent_badge_logo/' . $agentBadge->logo);

            if (gettype($logo) == 'string') {
                $logo_name = 'logo_' . time() . '.' . 'png';
                $logo_content = base64_decode($logo);
            } else {
                $logo_name = 'logo_' . time() . '_' . $logo->getClientOriginalName();
                $logo_content = file_get_contents($logo);
            }
            Storage::disk('dospace')->put('agent_badge_logo/' . $logo_name, $logo_content);
            Storage::setVisibility('agent_badge_logo/' . $logo_name, "public");
        }

        $agentBadge->name =  isset($data['name']) ? $data['name'] : $agentBadge->name;
        $agentBadge->deposit = isset($data['deposit']) ? $data['deposit'] : $agentBadge->deposit ;
        $agentBadge->logo =  isset($data['logo']) ? $logo_name :  $agentBadge->logo;
        $agentBadge->monthly_reward = isset($data['monthly_reward']) ? $data['monthly_reward'] : $agentBadge->monthly_reward;
        $agentBadge->delivery_points = isset($data['delivery_points']) ? $data['delivery_points'] : $agentBadge->delivery_points;
        $agentBadge->weekly_payment = isset($data['weekly_payment']) ?$data['weekly_payment'] : $agentBadge->weekly_payment;
        $agentBadge->monthly_good_credit = isset($data['monthly_good_credit']) ? $data['monthly_good_credit'] : $agentBadge->monthly_good_credit;

        if ($agentBadge->isDirty()) {
            $agentBadge->save();
        }

        return $agentBadge->refresh();
    }

    /**
     * @param AgentBadge $agentBadge
     */
    public function destroy(AgentBadge $agentBadge)
    {
        Storage::disk('dospace')->delete('agent_badge_logo/' . $agentBadge->logo);
        $deleted = $this->deleteById($agentBadge->id);

        if ($deleted) {
            $agentBadge->save();
        }
    }
}
