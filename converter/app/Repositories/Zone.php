<?php


namespace App\Repositories;


use DateTime;

class Zone extends AbstractRepository
{
    /**
     * Find zone by city id and utc time
     *
     * @param $id
     * @param DateTime $time
     *
     * @return mixed
     *
     */
    public static function findZoneUtc($id, DateTime $time)
    {
        $stm = static::$_connection->prepare('SELECT * FROM `zones` WHERE `city_id` = ? AND `utc_start` <= ? AND (`utc_end` <= ? OR `utc_end` IS NULL) LIMIT 1');
        if (!$stm->execute([$id, $time->getTimestamp(), $time->getTimestamp()])) {
            throw new \RuntimeException('error fetching zone');
        }

        return $stm->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Find zone by city id and local time
     *
     * @param $id
     * @param DateTime $time
     *
     * @return mixed
     *
     */
    public static function findZoneLocal($id, DateTime $time)
    {
        $stm = static::$_connection->prepare('SELECT * FROM `zones` WHERE `city_id` = ? AND `local_start` <= ? AND (`local_end` <= ? OR `local_end` IS NULL) LIMIT 1');
        if (!$stm->execute([$id, $time->format('Y-m-d H:i:s'), $time->format('Y-m-d H:i:s')])) {
            throw new \RuntimeException('error fetching zone');
        }

        return $stm->fetch(\PDO::FETCH_ASSOC);
    }
}