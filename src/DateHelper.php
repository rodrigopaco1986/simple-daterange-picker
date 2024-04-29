<?php

namespace Rpj\Daterangepicker;

use Exception;
use Illuminate\Support\Carbon;

class DateHelper
{
    const ALL = 'All';

    const TODAY = 'Today';

    const YESTERDAY = 'Yesterday';

    const LAST_2_DAYS = 'Last 2 days';

    const LAST_7_DAYS = 'Last 7 days';

    const THIS_WEEK = 'This week';

    const LAST_WEEK = 'Last week';

    const LAST_30_DAYS = 'Last 30 days';

    const THIS_MONTH = 'This month';

    const LAST_MONTH = 'Last month';

    const LAST_6_MONTHS = 'Last 6 months';

    const THIS_YEAR = 'This year';

    public static function defaultRanges(): array
    {
        return [
            self::TODAY => [Carbon::today(), Carbon::today()],
            self::YESTERDAY => [Carbon::yesterday(), Carbon::yesterday()],
            self::LAST_7_DAYS => [Carbon::today()->subDays(6), Carbon::today()],
            self::LAST_30_DAYS => [Carbon::today()->subDays(29), Carbon::today()],
            self::THIS_MONTH => [Carbon::today()->startOfMonth(), Carbon::today()],
            self::LAST_MONTH => [Carbon::today()->subMonth()->startOfMonth(), Carbon::today()->subMonth()->endOfMonth()],
            self::THIS_YEAR => [Carbon::today()->startOfYear(), Carbon::today()],
        ];
    }

    public static function getParsedDatesGroupedRanges($value): array
    {
        if ($value == self::ALL)
            return [null, null];

        $start = Carbon::now();
        $end = $start->clone();

        switch ($value) {
            case self::TODAY:
                break;
            case self::YESTERDAY:
                $start->subDay(1);
                $end = $start->clone();
                break;
            case self::LAST_2_DAYS:
                $start->subDays(1);
                break;
            case self::LAST_7_DAYS:
                $start->subDays(6);
                break;
            case self::THIS_WEEK:
                $start->startOfWeek(Carbon::MONDAY);
                break;
            case self::LAST_WEEK:
                $start->startOfWeek(Carbon::MONDAY)->subWeek(1);
                $end = $start->clone()->endOfWeek(Carbon::SUNDAY);
                break;
            case self::LAST_30_DAYS:
                $start->subDays(30);
                break;
            case self::THIS_MONTH:
                $start->startOfMonth();
                break;
            case self::LAST_MONTH:
                $start->startOfMonth()->subMonth();
                $end = $start->clone()->endOfMonth();
                break;
            case self::LAST_6_MONTHS:
                $start->subMonths(6);
                break;
            case self::THIS_YEAR:
                $start->startOfYear();
                break;
            default:
                //Ex. 2020-06-15 to 2023-06-15
                $parsed = explode(' to ', $value);
                if (count($parsed) == 1) {
                    $start = Carbon::createFromFormat('Y-m-d', $value);
                    $end = $start->clone();
                } elseif (count($parsed) == 2) {
                    $start = Carbon::createFromFormat('Y-m-d', $parsed[0]);
                    $end = Carbon::createFromFormat('Y-m-d', $parsed[1]);
                } else {
                    throw new Exception('Date range picker: Date format incorrect.');
                }
        }

        return [
            $start->setTime(0, 0, 0),
            $end->setTime(23, 59, 59),
        ];
    }
}
