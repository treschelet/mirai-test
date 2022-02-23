<?php

$loader = require dirname(__DIR__).'/vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$db = new \PDO('mysql:dbname='.$_ENV['DB_NAME'].';host='.$_ENV['DB_HOST'].';port='.$_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASS']);

\App\Services\TimeZoneApi::setKey($_ENV['API_KEY']);

$lastId = '';
$stmt = $db->prepare('SELECT * FROM `city` WHERE `id` > ? ORDER BY `id` LIMIT 10 FOR UPDATE');
$zoneStmt = $db->prepare('INSERT INTO `zones` (`city_id`, `offset`, `utc_start`, `utc_end`, `local_start`, `local_end`, `dst`) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `offset` = ?, `utc_end` = ?, `local_end` = ?');
$maxTime = new \DateTime('+3 month', new \DateTimeZone('UTC'));
do {
    $db->beginTransaction();
    if (!$stmt->execute([$lastId])) {
        $db->rollBack();
        break;
    }

    $cities = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    foreach ($cities as $city) {
        echo $city['name'].PHP_EOL;
        $lastId = $city['id'];
        $time = new \DateTime('@'.time());
        try {
            while ($time < $maxTime) {
                sleep(1);
                $timeZone = \App\Services\TimeZoneApi::getTimeZone($city['latitude'], $city['longitude'], $time);
                $interval = new \DateInterval('PT' . abs($timeZone['gmtOffset']) . 'S');
                if ($timeZone['gmtOffset'] < 0) {
                    $interval->invert;
                }
                $localStart = (new \DateTime('@' . $timeZone['zoneStart']))->add($interval)->format('Y-m-d H:m:s');
                $localEnd = $timeZone['zoneEnd'] ? (new \DateTime('@' . $timeZone['zoneEnd']))->add($interval)->format('Y-m-d H:i:s') : null;
                $zoneStmt->execute([$city['id'], $timeZone['gmtOffset'], $timeZone['zoneStart'], $timeZone['zoneEnd'], $localStart, $localEnd, $timeZone['dst'], $timeZone['gmtOffset'], $timeZone['zoneEnd'], $localEnd]);
                if ($timeZone['zoneEnd']) {
                    $time = new \DateTime('@'.($timeZone['zoneEnd'] + 1));
                } else {
                    break;
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage().PHP_EOL;
            continue;
        }
    }
    $db->commit();
} while ($cities);
