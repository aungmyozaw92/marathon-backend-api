<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Gate;
use App\Repositories\BaseRepository;

class GateRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Gate::class;
    }

    /**
     * @param array $data
     *
     * @return Gate
     */
    public function create(array $data) : Gate
    {
        if (isset($data['name'])) {
            $name = getConvertedString($data['name']);
        }

        $gate = Gate::create([
            'name' => $name,
            // 'gate_rate' => $data['gate_rate'],
            'gate_debit' => isset($data['gate_debit']) ? $data['gate_debit'] : 0,
            'bus_station_id' => $data['bus_station_id'],
            'bus_id' => $data['bus_id'],
            'created_by' => auth()->user()->id
        ]);

        $accountRepository = new AccountRepository();
        $account = [
            'city_id' => $gate->bus_station->city_id,
            'accountable_type' => 'gate',
            'accountable_id' => $gate->id,
        ];
        $accountRepository->create($account);

        return $gate;
    }

    /**
     * @param Gate  $gate
     * @param array $data
     *
     * @return mixed
     */
    public function update(Gate $gate, array $data) : Gate
    {
        if (isset($data['name'])) {
            $name = getConvertedString($data['name']);
        }

        $gate->name = isset($data['name']) ? $name : $gate->name;
        // $gate->gate_rate = isset($data['gate_rate']) ? $data['gate_rate'] : $gate->gate_rate;
        $gate->gate_debit = isset($data['gate_debit']) ? $data['gate_debit'] : $gate->gate_debit;
        $gate->bus_id = isset($data['bus_id']) ? $data['bus_id'] : $gate->bus_id;
        $gate->bus_station_id = isset($data['bus_station_id']) ? $data['bus_station_id'] : $gate->bus_station_id;

        if ($gate->isDirty()) {
            $gate->updated_by = auth()->user()->id;
            $gate->save();
        }

        if (!$gate->account) {
            $accountRepository = new AccountRepository();
            $account = [
                'city_id' => $gate->bus_station->city_id,
                'accountable_type' => 'gate',
                'accountable_id' => $gate->id,
            ];
            $accountRepository->create($account);
        }

        return $gate->refresh();
    }

    /**
     * @param Gate $gate
     */
    public function destroy(Gate $gate)
    {
        $deleted = $this->deleteById($gate->id);

        if ($deleted) {
            $gate->deleted_by = auth()->user()->id;
            $gate->save();
        }
    }
}
