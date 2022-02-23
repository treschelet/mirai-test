<?php


namespace App\Services;

use App\Repositories\Zone;
use DateTime;

class TimeConverter
{
    /**
     * @param $city
     * @param DateTime $utc
     *
     * @return DateTime
     *
     * @throws \Exception
     */
    public static function getLocal($city, DateTime $utc): DateTime
    {
        try {
            if ($zone = Zone::findZoneUtc($city['id'], $utc)) {
                $offset = $zone['offset'];
            } else {
                $timeZone = TimeZoneApi::getTimeZone($city['latitude'], $city['longitude'], $utc);
                $offset = $timeZone['gmtOffset'];
            }

            $interval = new \DateInterval('PT'.abs($offset).'S');
            if ($offset < 0) {
                $interval->invert = 1;
            }
        } catch (\Exception $e) {
            throw new \RuntimeException('Error converting to local time: '.$e->getMessage());
        }

        $local = clone $utc;
        $local->add($interval);

        return $local;
    }

    public static function getUtc($city, DateTime $local): DateTime
    {
        try {
            if ($zone = Zone::findZoneLocal($city['id'], $local)) {
                $offset = $zone['offset'];
            } else {
                throw new \RuntimeException('Error find zone');
            }

            $interval = new \DateInterval('PT'.abs($offset).'S');
            if ($offset < 0) {
                $interval->invert = 1;
            }
        } catch (\Exception $e) {
            throw new \RuntimeException('Error converting to local time');
        }

        $utc = clone $local;
        $utc->sub($interval);

        return $utc;
    }
}