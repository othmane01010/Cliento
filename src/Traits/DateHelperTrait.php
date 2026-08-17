<?php

namespace App\Traits;

use DateTimeImmutable;
use DateInterval;

trait DateHelperTrait
{
   
    public static function calculateEndDate(string $startDate, int $durationDays): string
    {
        $start = new DateTimeImmutable($startDate);
        $end = $start->add(new DateInterval("P{$durationDays}D"));

        return $end->format('Y-m-d');
    }

    public static function calculateRemainingDays(string $endDate): int
    {
        $today = new DateTimeImmutable('today');
        $end = new DateTimeImmutable($endDate);

        $diff = $today->diff($end);
        return $diff->invert ? -$diff->days : (int) $diff->days;
    }


    public static function determineSubscriptionStatus(string $endDate, int $expiringThresholdDays = 7): string
    {
        $remainingDays = self::calculateRemainingDays($endDate);

        if ($remainingDays < 0) {
            return 'EXPIRED';
        }

        if ($remainingDays <= $expiringThresholdDays) {
            return 'EXPIRING_SOON';
        }

        return 'ACTIVE';
    }
}