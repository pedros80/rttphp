<?php

namespace Pedros80\RTTphp\Contracts;

use stdClass;

interface Locations
{
    public function search(
        string $station,
        ?string $toStation=null,
        ?string $date=null,
        ?string $time=null,
        bool $arrivals=false
    ): stdClass;
}
