<?php

namespace mmerlijn\LaravelSalt\Helpers;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Carbon;

class DateInput
{
    public static function string(string $date, string $format = 'Y-m-d'): string
    {
        try {
            return Carbon::parse($date)->startOfDay()->format($format);
        } catch (InvalidFormatException $e) {
            return now()->startOfDay()->format($format);
        }
    }

    public static function carbon(string $date): Carbon
    {
        try {
            return Carbon::parse($date)->startOfDay();
        } catch (InvalidFormatException $e) {
            return Carbon::now()->startOfDay();
        }
    }

    public static function nullOrDate(?string $date): ?string
    {
        if (!$date) {
            return null;
        }
        try {
            return Carbon::parse($date)->startOfDay()->format('Y-m-d');
        } catch (InvalidFormatException $e) {
            return null;
        }
    }

    private static function smartenUp(string $date): string
    {


        if (preg_match('/^[0-9]+$/', $date)) {
            if (strlen($date) == 6) {
                $date = substr($date, 0, 2) . "-" . substr($date, 2, 2) . "-" . substr($date, 4, 2);
            } elseif (strlen($date) == 8) {
                $date = substr($date, 0, 2) . "-" . substr($date, 2, 2) . "-" . substr($date, 4);
            }
        }
        if (str_contains($date, "-")) {
            $split = explode("-", $date);
            if (count($split) == 3) {
                if ((int)$split[2] < date('y')) {
                    $split[2] = 2000 + (int)$split[2];
                } elseif ((int)$split[2] < 100) {
                    $split[2] = 1900 + (int)$split[2];
                }

                return implode("-", $split);
            }
        }
        return $date;
    }
}
