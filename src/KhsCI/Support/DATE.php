<?php

declare(strict_types=1);

namespace KhsCI\Support;

class DATE
{
    /**
     * @param      $timestamp
     * @param bool $returnArray
     *
     * @return array|false|int PRC timestamp
     *
     * @throws \Exception
     */
    public static function parse(string $timestamp, bool $returnArray = false)
    {
        /*
         * 2018-05-02T04:15:49.011488700Z
         *
         * 2018-05-02T03:55:52Z
         *
         * 2018-05-02T12:02:20+08:00
         */

        date_default_timezone_set('PRC');

        $time = date_parse($timestamp);

        if (28800 === $time['zone']) {
            list($year, $month, $day, $hour, $minute, $second) = array_values($time);

            if ($returnArray) {
                return $time;
            }

            return mktime((int) $hour, (int) $minute, (int) $second, (int) $month, (int) $day, (int) $year);
        }

        if (0 === $time['zone']) {
            list($year, $month, $day, $hour, $minute, $second) = array_values($time);

            if ($returnArray) {
                return $time;
            }

            return mktime((int) $hour, (int) $minute, (int) $second, (int) $month, (int) $day, (int) $year) + 8 * 60 * 60;
        }

        throw new \Exception('Only Support UTC or PRC', 500);
    }
}
