<?php


namespace App\Repositories;

class City extends AbstractRepository
{
    /**
     * Find city by id
     *
     * @param $id
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public static function getCity($id)
    {
        $stm = static::$_connection->prepare('SELECT * FROM `city` WHERE `id` = ?');
        if (!$stm->execute([$id])) {
            throw new \RuntimeException('error fetching city');
        }

        return $stm->fetch(\PDO::FETCH_ASSOC);
    }
}