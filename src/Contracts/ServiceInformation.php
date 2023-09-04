<?php

namespace Pedros80\RTTphp\Contracts;

use stdClass;

interface ServiceInformation
{
    public function search(string $serviceId, string $date): stdClass;
}
