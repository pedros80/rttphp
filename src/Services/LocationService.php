<?php

namespace Pedros80\RTTphp\Services;

use Pedros80\RTTphp\Contracts\Locations;
use Pedros80\RTTphp\Services\Service;
use stdClass;

final class LocationService extends Service implements Locations
{
    public function search(
        string $station,
        ?string $toStation=null,
        ?string $date=null,
        ?string $time=null,
        bool $arrivals=false
    ): stdClass {
        $this->url = ["json/search/{$station}"];

        if ($toStation) {
            $this->url[] = "to/{$toStation}";
        }

        if ($date && $this->isDateFormatValid($date)) {
            $this->url[] = $date;

            if ($time && $this->isTimeFormatValid($time)) {
                $this->url[] = $time;
            }
        }

        if ($arrivals) {
            $this->url[] = 'arrivals';
        }

        return $this->get();
    }
}
