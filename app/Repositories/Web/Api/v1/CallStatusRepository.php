<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\CallStatus;
use App\Repositories\BaseRepository;

class CallStatusRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return CallStatus::class;
    }

    /**
     * @param array $data
     *
     * @return CallStatus
     */
    public function create(array $data) : CallStatus
    {
        return CallStatus::create([
            'status' => $data['status'],
            'status_mm' => $data['status_mm'],
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param CallStatus  $callStatus
     * @param array $data
     *
     * @return mixed
     */
    public function update(CallStatus $callStatus, array $data) : CallStatus
    {
        $callStatus->status = $data['status'];
        $callStatus->status_mm = $data['status_mm'];

        if($callStatus->isDirty()) {
            $callStatus->updated_by = auth()->user()->id;
            $callStatus->save();
        }

        return $callStatus->refresh();
    }

    /**
     * @param CallStatus $callStatus
     */
    public function destroy(CallStatus $callStatus)
    {
        $deleted = $this->deleteById($callStatus->id);

        if ($deleted) {
            $callStatus->deleted_by = auth()->user()->id;
            $callStatus->save();
        }
    }
}

