<?php

$loader = require dirname(__DIR__).'/vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$db = new \PDO('mysql:dbname='.$_ENV['DB_NAME'].';host='.$_ENV['DB_HOST'].';port='.$_ENV['DB_PORT'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
\App\Repositories\Zone::InitConnection($db);
\App\Repositories\City::InitConnection($db);
\App\Services\TimeZoneApi::setKey($_ENV['API_KEY']);
$router = new \Bramus\Router\Router();

$router->setNamespace('\App\Controllers');
$router->mount('/api/v1', static function() use ($router) {
    $router->post('/local', 'ApiController@getLocalTimeAction');
    $router->post('/utc', 'ApiController@getUtcTimeAction');
});

$router->run();
