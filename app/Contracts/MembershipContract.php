<?php

namespace App\Contracts;

interface MembershipContract
{
    public function checkCommission($hero, $sheet);
    public function loggingCommission($hero, $sheet, $zone, $count);
    public function earnPointPerVoucher($sheet, $voucher, $type);
    public function earnCommission($sheet, $vouchers);
    public function earnPointPerSheet($sheet);
    public function rebornHero($hero, $sheet);
}
