<?php
namespace App\Controllers;

use App\Repositories\City;
use App\Services\TimeConverter;

class ApiController extends BaseController
{
    public function getLocalTimeAction(): void
    {
        $cityId = $this->getParam('city_id');
        if (is_null($cityId)) {
            $this->jsonError('city_id is required');
            return;
        }
        $time = $this->getParam('time', time());
        try {
            $utcTime = new \DateTime('@' . $time);
        } catch (\Exception $e) {
            $this->jsonError($e->getMessage());
            return;
        }

        try {
            if (!($city = City::getCity($cityId))) {
                $this->jsonError('City not found');
                return;
            }
        } catch (\Exception $e) {
            $this->jsonError('Error getting city');
            return;
        }

        try {
            $localTime = TimeConverter::getLocal($city, $utcTime);
        } catch (\Exception $e) {
            $this->jsonError('Error converting time');
            return;
        }

        $this->json(['local' => $localTime->format('Y-m-d H:i:s')]);
    }

    public function getUtcTimeAction(): void
    {
        $cityId = $this->getParam('city_id');
        if (is_null($cityId)) {
            $this->jsonError('city_id is required');
            return;
        }
        $time = $this->getParam('time');
        if (is_null($time)) {
            $this->jsonError('time is require');
            return;
        }

        $localTime = \DateTime::createFromFormat('Y-m-d H:i:s', $time, new \DateTimeZone('UTC'));
        if ($localTime === false) {
            $this->jsonError('invalid time format');
            return;
        }

        try {
            if (!($city = City::getCity($cityId))) {
                $this->jsonError('City not found');
                return;
            }
        } catch (\Exception $e) {
            $this->jsonError('Error getting city');
            return;
        }

        try {
            $utcTime = TimeConverter::getUtc($city, $localTime);
        } catch (\Exception $e) {
            $this->jsonError('Error converting time');
            return;
        }

        $this->json(['utc' => $utcTime->getTimestamp()]);
    }
}