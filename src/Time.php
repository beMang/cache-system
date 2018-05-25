<?php

namespace bemang\Cache;

class Time
{
    public static function getValidInterval($interval = false)
    {
        if (is_numeric($interval)) {
            return new \DateInterval('P0Y0DT0H' . $interval . 'M');
        } elseif ($interval instanceof \DateInterval) {
            return $interval;
        } else {
            return new \DateInterval('P0Y0DT0H' . FileCache::DEFAULT_TTL . 'M');
        }
    }

    public static function getMinuteOfDateInterval(\DateInterval $interval)
    {
        $hoursInMinute = $interval->h * 60;
        $dayInMinute = $interval->d * 1440;
        $monthInMinute = $interval->m * 43800;
        $yearInMinute = $interval->y * 525600;
        $totalMinute = $hoursInMinute + $dayInMinute + $monthInMinute + $yearInMinute + $interval->i;
        return $totalMinute;
    }
}
