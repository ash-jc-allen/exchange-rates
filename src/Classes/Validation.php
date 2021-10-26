<?php

namespace AshAllenDesign\ExchangeRates\Classes;

use AshAllenDesign\ExchangeRates\Exceptions\InvalidDateException;
use Carbon\Carbon;

class Validation
{
    public static function validateStartAndEndDates(Carbon $from, Carbon $to): void
    {
        self::validateDate($from);
        self::validateDate($to);

        if ($from->isAfter($to)) {
            throw new InvalidDateException('The \'from\' date must be before the \'to\' date.');
        }
    }

    public static function validateDate(Carbon $date): void
    {
        if (! $date->isPast()) {
            throw new InvalidDateException('The date must be in the past.');
        }

        $earliestPossibleDate = Carbon::createFromDate(1999, 1, 4)->startOfDay();

        if ($date->isBefore($earliestPossibleDate)) {
            throw new InvalidDateException('The date cannot be before 4th January 1999.');
        }
    }
}
