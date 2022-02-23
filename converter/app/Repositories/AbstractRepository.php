<?php


namespace App\Repositories;

abstract class AbstractRepository
{
    static protected \PDO $_connection;

    /**
     * Init repository connection
     *
     * @param \PDO $connection
     */
    public static function InitConnection(\PDO $connection): void
    {
        static::$_connection = $connection;
    }
}