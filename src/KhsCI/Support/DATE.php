<?php


namespace KhsCI\Support;


class DATE
{
    public static function parse($timestamp, $returnArray = false)
    {
        $array = explode('T', $timestamp);

        $year_month_day = explode('-', $array[0]);

        $time = explode('+', $array[1]);

        $hour_min_sen = explode(':', $time[0]);

        $year = $year_month_day[0];

        $month = $year_month_day[1];

        $day = $year_month_day[2];

        $hour = $hour_min_sen[0];

        $minute = $hour_min_sen[1];

        $second = $hour_min_sen[2];

        if ($returnArray) {
            return [
                'year' => $year,
                'month' => $month,
                'day' => $day,
                'hour' => $hour,
                'minute' => $minute,
                'second' => $second,
            ];
        }

        return mktime($hour, $minute, $second, $month, $day, $year);


    }
}
