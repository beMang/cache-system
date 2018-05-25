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
            return new \DateInterval('P0Y0DT0H' . self::DEFAULT_TTL . 'M');
        }
    }

    public static function getMinutOfInterval()
    {
    }
}
