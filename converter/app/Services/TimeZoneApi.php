<?php
namespace App\Services;

use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class TimeZoneApi
{
    static private string $_key;
    private static ?TimeZoneApi $_instance = null;

    private Client $client;
    private string $key;

    public static function setKey(string $key): void
    {
        self::$_key = $key;
    }

    private static function _getInstance(): TimeZoneApi
    {
        if (self::$_instance === null) {
            self::$_instance = new self(self::$_key);
        }

        return self::$_instance;
    }

    public function __construct(string $key)
    {
        $this->client = new Client(['base_uri' => 'http://api.timezonedb.com/v2.1/']);
        $this->key = $key;
    }

    /**
     * @param $lat
     * @param $lon
     * @param \DateTime|null $time
     *
     * @return array
     *
     * @throws \JsonException
     */
    public static function getTimeZone($lat, $lon, ?DateTime $time = null): array
    {
        return self::_getInstance()->apiTimeZone($lat, $lon, $time);
    }

    /**
     * @param $lat
     * @param $lon
     * @param DateTime|null $time
     *
     * @return array
     *
     * @throws \RuntimeException
     * @throws \JsonException
     */
    private function apiTimeZone($lat, $lon, ?DateTime $time = null): array
    {
        $params = [
            'key' => $this->key,
            'format' => 'json',
            'by' => 'position',
            'lat' => $lat,
            'lng' => $lon,
        ];
        if ($time !== null) {
            $params['time'] = $time->getTimestamp();
        }

        $response = $this->client->get('get-time-zone?'.http_build_query($params), [RequestOptions::HTTP_ERRORS => false]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Error getting timezone: '.$response->getStatusCode());
        }

        $body = $response->getBody()->getContents();

        return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
    }
}