<?php

namespace Pedros80\RTTphp\Services;

use Pedros80\RTTphp\Services\Service;

final class ServiceInformationService extends Service
{
    public function search(string $serviceId, string $date): array
    {
        $url = "json/service/{$serviceId}";

        if ($this->isDateFormatValid($date)) {
            $url = "{$url}/$date";
        }

        return $this->get($url);
    }
}
