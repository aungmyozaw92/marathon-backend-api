<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\LogStatus;
use App\Repositories\BaseRepository;

class LogStatusRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return LogStatus::class;
    }

    /**
     * @param array $data
     *
     * @return LogStatus
     */
    public function create(array $data) : LogStatus
    {
        if (isset($data['description_mm'])) {
            $description_mm = getConvertedString($data['description_mm']);
        }

        return LogStatus::create([
            'value'          => $data['value'],
            'description'    => $data['description'],
            'description_mm' => ($data['description_mm']) ? $description_mm : null,
            'created_by'     => auth()->user()->id
        ]);
    }

    /**
     * @param LogStatus  $LogStatus
     * @param array $data
     *
     * @return mixed
     */
    public function update(LogStatus $logStatus, array $data) : LogStatus
    {
        $logStatus->value = $data['value'];
        $logStatus->description = $data['description'];
        $logStatus->description_mm = $data['description_mm'];
        if ($logStatus->isDirty()) {
            $logStatus->updated_by = auth()->user()->id;
            $logStatus->save();
        }

        return $logStatus->refresh();
    }

    /**
     * @param LogStatus $logStatus
     */
    public function destroy(LogStatus $logStatus)
    {
        $deleted = $this->deleteById($logStatus->id);

        if ($deleted) {
            $logStatus->deleted_by = auth()->user()->id;
            $logStatus->save();
        }
    }
}
