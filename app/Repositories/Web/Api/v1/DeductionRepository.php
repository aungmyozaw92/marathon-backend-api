<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Deduction;
use App\Repositories\BaseRepository;

class DeductionRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Deduction::class;
    }

    /**
     * @param array $data
     *
     * @return Deduction
     */
    public function create(array $data) : Deduction
    {
        return Deduction::create([
            'points'        => $data['points'],
            'description'   => $data['description']
        ]);
    }

    /**
     * @param Deduction  $deduction
     * @param array $data
     *
     * @return mixed
     */
    public function update(Deduction $deduction, array $data) : Deduction
    {
        $deduction->points =  isset($data['points']) ? $data['points'] : $deduction->points;
        $deduction->description = isset($data['description']) ? $data['description'] : $deduction->description ;

        if ($deduction->isDirty()) {
            $deduction->save();
        }

        return $deduction->refresh();
    }

    /**
     * @param Deduction $deduction
     */
    public function destroy(Deduction $deduction)
    {
        $deleted = $this->deleteById($deduction->id);

        if ($deleted) {
            $deduction->save();
        }
    }
}
