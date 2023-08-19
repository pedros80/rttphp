<?php

namespace Pedros80\RTTphp\Services;

use Pedros80\RTTphp\Services\Service;

final class LocationService extends Service
{
    public function search(string $station, ?string $toStation=null, ?string $date=null, ?string $time=null): array
    {
        $url = "json/search/{$station}";

        if ($toStation) {
            $url = "{$url}/to/{$toStation}";
        }

        if ($date && $this->isDateFormatValid($date)) {
            $url = "{$url}/{$date}";

            if ($time && $this->isTimeFormatValid($time)) {
                $url = "{$url}/{$time}";
            }
        }

        return $this->get($url);
    }
}
