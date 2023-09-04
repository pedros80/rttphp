<?php

namespace Pedros80\RTTphp\Services;

use Pedros80\RTTphp\Contracts\ServiceInformation;
use Pedros80\RTTphp\Exceptions\InvalidServiceIdFormat;
use Pedros80\RTTphp\Services\Service;
use stdClass;

final class ServiceInformationService extends Service implements ServiceInformation
{
    public function search(string $serviceId, string $date): stdClass
    {
        $this->isServiceIdValid($serviceId);

        $this->url = ["json/service/{$serviceId}"];

        if ($this->isDateFormatValid($date)) {
            $this->url[] = $date;
        }

        return $this->get();
    }

    private function isServiceIdValid(string $serviceId): bool
    {
        if (!preg_match('/^[A-Z][0-9]{5}$/', $serviceId)) {
            throw InvalidServiceIdFormat::fromString($serviceId);
        }

        return true;
    }
}
