<?php

namespace App\Services;

use App\Contracts\MembershipContract;
use App\Models\Staff;
use App\Models\DeliSheetVoucher;
use App\Models\Voucher;
use App\Models\HeroBadge;
use App\Models\PointLog;
use App\Models\CommissionLog;
use App\Models\Meta;
use App\Repositories\Web\Api\v1\JournalRepository;

class HeroMembershipService implements MembershipContract
{
    public function checkCommission($hero, $sheet)
    {
        $commissionableType = class_basename($sheet);
        $commissionableId = $sheet->id;
        $alreadyEarn = CommissionLog::where([['commissionable_type', $commissionableType], ['commissionable_id', $commissionableId], ['staff_id', $hero->id]])->count();
        return $alreadyEarn;
    }
    public function loggingCommission($hero, $sheet, $zone, $count)
    {
        $pickp_commission = Meta::where('key', 'pickup_commission')->first();
        $zone_commission = class_basename($sheet) == 'DeliSheet' ? 0
            : (class_basename($sheet) == 'Pickup' ? (int) $pickp_commission->value
                : $this->getZoneCommission($hero, $zone));
        $voucher_commission = class_basename($sheet) == 'DeliSheet' ? $sheet->commission_amount : (class_basename($sheet) == 'Pickup' && $zone_commission > 0 ? (int) $pickp_commission->value * $count : 0);
        $commission_log = new CommissionLog([
            'staff_id' => $hero->id,
            'zone_id' => $zone->id,
            'zone_commission' => $zone_commission,
            'voucher_commission' => $voucher_commission,
            'num_of_vouchers' => $count,
            'created_by' => auth()->user() ? auth()->user()->id : null
        ]);
        $sheet->commission_logs()->save($commission_log);
    }
    public function earnPointPerVoucher($sheet, $vouchers, $type)
    {
        if ($type == 'Delisheet') {
            $hero = Staff::find($sheet->delivery_id);
            if ($hero && !isBlackList($hero)) {
                $this->pointPerVoucher($hero, $vouchers, $sheet);
            }
            return;
        }
        if ($type == 'Pickup') {
            $hero = Staff::find($sheet->pickuped_by_id);
            if ($hero && !isBlackList($hero)) {
                $this->pointPerVoucher($hero, $vouchers, $sheet);
            }
            return;
        }
    }
    public function earnPointPerSheet($sheet)
    {
        $hero = Staff::find($sheet->delivery_id);
        if ($hero && !isBlackList($hero)) {
            $this->pointPerSheet($hero, $sheet);
        }
        return;
    }

    public function earnCommission($sheet, $vouchers)
    {

        if ($sheet->is_commissionable) {
            foreach ($vouchers as $delivered_voucher) {
                \DB::table('deli_sheet_vouchers')->where('delisheet_id', $sheet->id)
                    ->where('voucher_id', $delivered_voucher['id'])
                    ->update(['updated_by' => auth()->user()->id]);
                // $voucher = Voucher::find($delivered_voucher['id']);
                // $voucher->delivery_commission = isFreelancer($sheet->delivery)
                // ? $voucher->receiver_zone->outsource_rate
                // : $voucher->receiver_zone->zone_commission;;
                // $voucher->save();
            }
        }
        return;
    }

    private function pointPerVoucher($hero, $total_voucher, $sheet)
    {
        if (isHero($sheet)) {
            $sheetPoint = $hero->hero_badge()->exists() ? $total_voucher * $hero->hero_badge->multiplier_point : $total_voucher;
            $resourceableType = class_basename($sheet);
            $resourceableId = $sheet->id;
            $alreadyEarn = PointLog::where([['resourceable_type', $resourceableType], ['resourceable_id', $resourceableId], ['staff_id', $hero->id], ['points', $sheetPoint]])->first();
            if (!$alreadyEarn) {
                $this->loggingPoint($hero, $sheet, $sheetPoint);
                $hero->increment('points', $sheetPoint);
                $hero->refresh();
            }
        }
        return;
    }

    private function pointPerSheet($hero, $sheet)
    {
        if (isHero($sheet)) {
            $sheetPoint = $hero->hero_badge()->exists() ? $hero->hero_badge->multiplier_point : 1;
            $resourceableType = class_basename($sheet);
            $resourceableId = $sheet->id;
            $alreadyEarn = PointLog::where([['resourceable_type', $resourceableType], ['resourceable_id', $resourceableId], ['staff_id', $hero->id], ['points', $sheetPoint]])->first();
            if (!$alreadyEarn) {
                $this->loggingPoint($hero, $sheet, $sheetPoint);
                $hero->increment('points', $sheetPoint);
                $hero->refresh();
            }
        }
        return;
    }
    private function loggingPoint($hero, $sheet, $sheetPoint)
    {
        $point_log = new PointLog([
            'staff_id' => $hero->id,
            'points' => $sheetPoint,
            'status' => 'Add',
            'hero_badge_id' => $hero->hero_badge_id,
            'created_by' => auth()->user() ? auth()->user()->id : null
        ]);
        $sheet->point_logs()->save($point_log);
        return;
    }

    private function getZoneCommission($hero, $zone)
    {
        $commission = isFreelancer($hero) ? $zone->outsource_rate
            : (isFreelancerCar($hero) ? $zone->outsource_car_rate : $zone->zone_commission);
        return $commission;
    }

    // for cron
    public function rebornHero($hero, $sheet)
    {
        $this->loggingPoint($hero, $sheet, $hero->points);
        if (!$hero->hero_badge) {
            $hero->hero_badge_id = 1;
        }
        if ($hero->hero_badge) {
            if ($hero->points > $hero->hero_badge->maintainence_point) {
                $upgradeable_badge = HeroBadge::find($hero->hero_badge_id + 1);
                if (!$upgradeable_badge) {
                    $upgradeable_badge = HeroBadge::find($hero->hero_badge_id);
                }
                if ($hero->points >= $upgradeable_badge->maintainence_point) {
                    $hero->hero_badge_id = $upgradeable_badge->id;
                }
            }
            if ($hero->points < $hero->hero_badge->maintainence_point) {
                $hero->hero_badge_id =  $hero->hero_badge_id > 1 ? $hero->hero_badge_id - 1 : 1;
            }
        }
        $hero->points = 0;
        if ($hero->isDirty()) {
            $hero->save();
        }
        return;
    }
}
