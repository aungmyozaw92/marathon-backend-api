<?php

namespace App\Repositories\Mobile\Api\v1\Delivery;

use App\Models\ReturnSheet;
use App\Models\Staff;
use App\Repositories\BaseRepository;
use App\Repositories\Mobile\Api\v1\Delivery\AttachmentRepository;
use App\Repositories\Mobile\Api\v1\Delivery\CalculateAmountRepository;
use App\Contracts\MembershipContract;
use App\Repositories\Web\Api\v1\JournalRepository;

class ReturnSheetRepository extends BaseRepository
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
        return ReturnSheet::class;
    }

    public function upload($data)
    {
        if (isset($data['note'])) {
            $note = getConvertedString($data['note']);
        }

        $return_sheet = ReturnSheet::findOrFail($data['return_sheet_id']);
        $return_sheet->is_returned = isset($data['is_returned']) ? $data['is_returned'] : 1;
        // upload attachment
        $file_data = [
            'return_sheet_id' => $return_sheet->id,
            'file' => isset($data['file']) ? $data['file'] : null,
            'note' => isset($data['note']) ? $note : null,
            'latitude' => isset($data['latitude']) ? $data['latitude'] : null,
            'longitude' => isset($data['longitude']) ? $data['longitude'] : null,
            'is_sign' => isset($data['is_sign']) ? $data['is_sign'] : 0,
        ];
        $attachmentRepository = new AttachmentRepository();
        $attachment = $attachmentRepository->create($file_data);
        if ($attachment) {
            if ($return_sheet->isDirty()) {

                // $calculate_pointRepository = new CalculateAmountRepository();
                // $points = $calculate_pointRepository->calculate_points($return_sheet->vouchers[0]);

                // $staff = auth()->user();
                // $staff->points += $points;
                // $staff->save();

                $return_sheet->updated_by = auth()->user()->id;
                $return_sheet->is_came_from_mobile = 1;
                $return_sheet->actby_mobile = auth()->user()->id;
                $hero = Staff::findOrFail($return_sheet->delivery_id);
                $alreadyEarned = $this->membershipContract->checkCommission($hero, $return_sheet);
                if ($hero && $return_sheet->getQtyAttribute() > 0 && $return_sheet->is_commissionable && isHero($return_sheet) && $alreadyEarned < 1) {
                    $return_sheet->commission_amount = isFreelancer($return_sheet->delivery)
                        ? $return_sheet->merchant_associate->zone->outsource_rate
                        : (isFreelancerCar($return_sheet->delivery) ? $return_sheet->merchant_associate->zone->outsource_car_rate
                            : $return_sheet->merchant_associate->zone->zone_commission);
                    $journalRepository = new JournalRepository();
                    $authBranchAccountId  = $return_sheet->delivery->city->branch->account->id;
                    $hero_account = $hero->account ? $hero->account : $journalRepository->create_account($hero, 'Staff');
                    $journalRepository->JournalCreateData($authBranchAccountId, $hero_account->id, $return_sheet->commission_amount, $return_sheet, 'ReturnSheet', 1);
                    $this->membershipContract->loggingCommission($return_sheet->delivery, $return_sheet, $return_sheet->merchant_associate->zone, $return_sheet->getQtyAttribute());
                }
                if (
                    $hero && $return_sheet->getQtyAttribute() > 0 && $return_sheet->is_pointable && isHero($return_sheet)
                    && !isBlackList($return_sheet->delivery) && !isFreelancerCar($return_sheet->delivery)
                ) {
                    $this->membershipContract->earnPointPerSheet($return_sheet);
                }
                $return_sheet->returned_date = date('Y-m-d H:i:s');
                $return_sheet->save();
                if ($return_sheet->vouchers) {
                    foreach ($return_sheet->vouchers as $voucher) {
                        $voucher->is_return = 1;
                        $voucher->returned_date = date('Y-m-d H:i:s');
                        $voucher->end_date = date('Y-m-d H:i:s');
                        $voucher->save();
                    }
                    //    $return_sheet->vouchers()->update(['is_return' => 1, 'end_date' => date('Y-m-d H:i:s')]);
                }
            }
        }
        return $attachment;
    }
}
