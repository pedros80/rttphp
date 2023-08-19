<?php

namespace Pedros80\RTTphp\Exceptions;

use Exception;

final class ServiceNotFound extends Exception
{
    private function __construct(string $message)
    {
        parent::__construct($message, 404);
    }

    public static function fromUrl(string $url): ServiceNotFound
    {
        return new ServiceNotFound("Could not find service from '{$url}'. Please check url.");
    }
}
