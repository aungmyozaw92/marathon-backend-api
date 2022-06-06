<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Zone;
use App\Repositories\BaseRepository;

class ZoneRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Zone::class;
    }

    /**
     * @param array $data
     *
     * @return Zone
     */
    public function create(array $data) : Zone
    {
        if (isset($data['note'])) {
            $note = getConvertedString($data['note']);
        }

        if (isset($data['name_mm'])) {
            $name_mm = getConvertedString($data['name_mm']);
        }

        $zone = Zone::create([
            'name' => $data['name'],
            'name_mm' => isset($data['name_mm'])? $name_mm : null,
            'zone_rate' => $data['zone_rate'],
            'zone_agent_rate' => $data['zone_agent_rate'],
            'diff_zone_rate' => isset($data['diff_zone_rate'])? $data['diff_zone_rate'] : 0,
            'zone_commission' => isset($data['zone_commission'])? $data['zone_commission'] : 0,
            'outsource_rate' => isset($data['outsource_rate'])? $data['outsource_rate'] : 0,
            'outsource_car_rate' => isset($data['outsource_car_rate'])? $data['outsource_car_rate'] : 0,
            'city_id' => $data['city_id'],
            'note' => isset($data['note']) ? $note : null,
            'is_deliver' => $data['is_deliver'],
            'created_by' => auth()->user()->id
        ]);
        
        $accountRepository = new AccountRepository();
        $account = [
                'city_id' => $zone->city_id,
                'accountable_type' => 'zone',
                'accountable_id' => $zone->id,
            ];
        $accountRepository->create($account);

        return $zone;
    }

    /**
     * @param Zone  $zone
     * @param array $data
     *
     * @return mixed
     */
    public function update(Zone $zone, array $data) : Zone
    {
        if (isset($data['note'])) {
            $note = getConvertedString($data['note']);
        }
        if (isset($data['name_mm'])) {
            $name_mm = getConvertedString($data['name_mm']);
        }

        $zone->name = $data['name'];
        $zone->name_mm = isset($data['name_mm']) ? $name_mm : $zone->name_mm;
        $zone->zone_rate = $data['zone_rate'];
        $zone->zone_agent_rate = $data['zone_agent_rate'];
        $zone->city_id = $data['city_id'];
        $zone->note = isset($data['note']) ? $note : null;
        $zone->is_deliver = isset($data['is_deliver']) ? $data['is_deliver'] : $zone->is_deliver;
        $zone->zone_commission = isset($data['zone_commission']) ? $data['zone_commission'] : $zone->zone_commission;
        $zone->outsource_rate = isset($data['outsource_rate']) ? $data['outsource_rate'] : $zone->outsource_rate;
        $zone->outsource_car_rate = isset($data['outsource_car_rate']) ? $data['outsource_car_rate'] : $zone->outsource_car_rate;
        $zone->diff_zone_rate = isset($data['diff_zone_rate']) ? $data['diff_zone_rate'] : $zone->diff_zone_rate;

        if ($zone->isDirty()) {
            $zone->updated_by = auth()->user()->id;
            $zone->save();
        }

        if (!$zone->account) {
            $accountRepository = new AccountRepository();
            $account = [
                'city_id' => $zone->city_id,
                'accountable_type' => 'zone',
                'accountable_id' => $zone->id,
            ];
            $accountRepository->create($account);
        }

        return $zone->refresh();
    }

    /**
     * @param Zone $zone
     */
    public function destroy(Zone $zone)
    {
        $deleted = $this->deleteById($zone->id);

        if ($deleted) {
            $zone->deleted_by = auth()->user()->id;
            $zone->save();
        }
    }
}
