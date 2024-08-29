<?php

namespace Pedros80\RTTphp\Exceptions;

use Exception;

final class InvalidServiceResponse extends Exception
{
    private function __construct(string $message)
    {
        parent::__construct($message, 400);
    }

    public static function new(): InvalidServiceResponse
    {
        return new InvalidServiceResponse('Invalid Service Response - could not decode to object');
    }
}
