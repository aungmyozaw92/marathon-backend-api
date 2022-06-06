<?php

namespace App\Repositories\Mobile\Api\v1\Delivery;

use App\Models\Waybill;
use App\Repositories\BaseRepository;
use App\Repositories\Mobile\Api\v1\Delivery\AttachmentRepository;
use App\Contracts\MembershipContract;

class WaybillRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected $membershipContract;
    public function __construct(MembershipContract $membershipContract)
    {
        $this->membershipContract = $membershipContract;
    }
    public function model()
    {
        return Waybill::class;
    }

    public function upload($data)
    {
        if (isset($data['note'])) {
            $note = getConvertedString($data['note']);
        }

        $waybill = Waybill::findOrFail($data['waybill_id']);
        $waybill->is_delivered = $data['is_delivered'];
        $waybill->delivered_date = date('Y-m-d H:i:s');
        $waybill->actual_bus_fee = $data['actual_bus_fee'];
        // upload attachment
        $file_data = [
            'waybill_id' => $waybill->id,
            'file' => isset($data['file']) ? $data['file'] : null,
            'note' => isset($data['note']) ? $note : null,
            'latitude' => isset($data['latitude']) ? $data['latitude'] : null,
            'longitude' => isset($data['longitude']) ? $data['longitude'] : null,
        ];
        $attachmentRepository = new AttachmentRepository();
        $attachment = $attachmentRepository->create($file_data);
        if ($attachment) {
            if ($waybill->isDirty()) {
                // $staff = auth()->user();
                // $staff->points += 2;
                // $staff->save();

                $waybill->updated_by = auth()->user()->id;
                $waybill->is_came_from_mobile = 1;
                $waybill->actby_mobile = auth()->user()->id;
                $waybill->save();
                // $this->membershipContract->earnPointPerSheet($waybill);
            }
        }
        return $attachment;
    }
}
